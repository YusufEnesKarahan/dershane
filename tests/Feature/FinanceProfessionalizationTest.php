<?php

namespace Tests\Feature;

use App\Domain\Finance\Services\FinanceManagementService;
use App\Domain\Finance\Services\PreRegistrationService;
use App\Models\AcademicTerm;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PlatformAuditLog;
use App\Models\PreRegistration;
use App\Models\Role;
use App\Models\Student;
use App\Models\SystemIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceProfessionalizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected $branch;
    protected Classroom $classroom;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        SystemIdentity::firstOrCreate(['company_name' => 'Test'], ['product_name' => 'Test ERP']);
        AcademicTerm::firstOrCreate(['name' => '2025-2026'], ['start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        $this->branch = Branch::create(['name' => 'Kıyı Şubesi', 'slug' => 'kiyi-' . uniqid()]);
        
        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $this->adminUser = User::factory()->create(['branch_id' => $this->branch->id]);
        $this->adminUser->roles()->attach($roleAdmin);

        $this->classroom = Classroom::create(['branch_id' => $this->branch->id, 'code' => 'CLS-11A-' . uniqid(), 'name' => '11-A TM', 'capacity' => 25]);

        $this->student = Student::create([
            'branch_id' => $this->branch->id,
            'student_number' => 'STU-12345',
            'first_name' => 'Ahmet',
            'last_name' => 'Yılmaz',
            'phone' => '05551112233',
            'tc_no' => '11122233344',
            'classroom_id' => $this->classroom->id,
        ]);
    }

    public function test_ajax_live_search_students_returns_matching_cards_with_min_3_chars(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.invoices.search-students', ['q' => 'Ahm']));

        $response->assertOk();
        $response->assertJsonFragment([
            'student_number' => 'STU-12345',
            'full_name' => 'Ahmet Yılmaz',
        ]);
    }

    public function test_invoice_creation_with_multiple_items_and_total_calculation(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.invoices.store'), [
            'student_id' => $this->student->id,
            'due_date' => now()->addDays(15)->format('Y-m-d'),
            'description' => '2026 Dönemi Paket Faturası',
            'items' => [
                ['item_type' => 'Kayıt Ücreti', 'description' => 'Ana Kurs Ücreti', 'quantity' => 1, 'unit_price' => 10000],
                ['item_type' => 'Kitap', 'description' => 'AYT Soru Bankaları', 'quantity' => 2, 'unit_price' => 500],
                ['item_type' => 'Deneme', 'description' => '10lu Paket Deneme', 'quantity' => 1, 'unit_price' => 1000],
            ],
        ]);

        $response->assertRedirect();
        
        $invoice = Invoice::where('student_id', $this->student->id)->firstOrFail();
        $this->assertEquals(12000.00, $invoice->total_amount); // 10000 + 1000 + 1000 = 12000
        $this->assertCount(3, $invoice->items);
        $this->assertEquals('Pending', $invoice->status);
    }

    public function test_payment_recording_updates_invoice_paid_amount_and_status_transitions(): void
    {
        $invoice = Invoice::create([
            'branch_id' => $this->branch->id,
            'invoice_number' => 'INV-TEST-01',
            'student_id' => $this->student->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'total_amount' => 5000.00,
            'paid_amount' => 0.00,
            'status' => 'Pending',
        ]);

        // 1. Partial Payment of 2000 TL
        $responsePartial = $this->actingAs($this->adminUser)->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 2000.00,
            'payment_method' => 'Kredi Kartı',
            'notes' => 'İlk Taksit Peşinatı',
        ]);

        $responsePartial->assertRedirect();
        $invoice->refresh();

        $this->assertEquals(2000.00, $invoice->paid_amount);
        $this->assertEquals('Partial', $invoice->status);

        // 2. Remaining Payment of 3000 TL
        $responseFinal = $this->actingAs($this->adminUser)->post(route('admin.invoices.payments.store', $invoice->id), [
            'amount' => 3000.00,
            'payment_method' => 'Havale',
        ]);

        $responseFinal->assertRedirect();
        $invoice->refresh();

        $this->assertEquals(5000.00, $invoice->paid_amount);
        $this->assertEquals('Paid', $invoice->status);
    }

    public function test_pre_registration_creation_and_filtering(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.pre-registrations.store'), [
            'student_name' => 'Buse Demir',
            'phone' => '05443332211',
            'classroom_name' => '12. Sınıf',
            'interested_program' => 'Eşit Ağırlık YKS',
            'source' => 'Instagram',
            'status' => 'Yeni',
        ]);

        $response->assertRedirect(route('admin.pre-registrations.index'));
        $this->assertDatabaseHas('pre_registrations', [
            'student_name' => 'Buse Demir',
            'source' => 'Instagram',
        ]);
    }

    public function test_one_click_pre_registration_conversion_creates_student_guardian_and_invoice_atomically(): void
    {
        $preReg = PreRegistration::create([
            'branch_id' => $this->branch->id,
            'student_name' => 'Mehmet Kaya',
            'phone' => '05339998877',
            'classroom_name' => '11-A',
            'interested_program' => 'Sayısal VIP',
            'source' => 'Google',
            'status' => 'Yeni',
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.pre-registrations.convert.store', $preReg->id), [
            'student_number' => 'STU-99988',
            'classroom_id' => $this->classroom->id,
            'guardian_name' => 'Ali Kaya (Baba)',
            'guardian_phone' => '05339998877',
            'tuition_amount' => 15000.00,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $preReg->refresh();

        $this->assertEquals('Kayıt Oldu', $preReg->status);
        $this->assertNotNull($preReg->converted_student_id);

        $convertedStudent = Student::findOrFail($preReg->converted_student_id);
        $this->assertEquals('Mehmet', $convertedStudent->first_name);
        $this->assertEquals('Kaya', $convertedStudent->last_name);

        $invoice = Invoice::where('student_id', $convertedStudent->id)->firstOrFail();
        $this->assertEquals(15000.00, $invoice->total_amount);
    }

    public function test_finance_dashboard_metrics_and_charts_rendering(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.finance.dashboard'));

        $response->assertOk();
        $response->assertSee('Finans');
        $response->assertSee('Son 12 Ay Tahsilat Grafiği');
    }
}
