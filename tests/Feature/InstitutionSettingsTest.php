<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Domain\Institution\Models\InstitutionSetting;
use App\Domain\Institution\Services\InstitutionSettingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\FeatureSeeder;
use Database\Seeders\PackageSeeder;

class InstitutionSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected InstitutionSettingService $settingService;
    protected Branch $branch1;
    protected Branch $branch2;
    protected User $adminUser1;
    protected User $adminUser2;
    protected User $teacherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \App\Http\Middleware\CheckOnboardingStatus::class,
        ]);

        $this->seed(FeatureSeeder::class);
        $this->seed(PackageSeeder::class);

        $this->settingService = app(InstitutionSettingService::class);

        // Branch 1
        $this->branch1 = Branch::create([
            'name' => 'Kadıköy Şubesi',
            'code' => 'KDK-01',
            'slug' => 'kdk-01',
            'status' => 'active',
        ]);
        $this->adminUser1 = User::factory()->create([
            'branch_id' => $this->branch1->id,
        ]);
        $adminRole = Role::firstOrCreate(['name' => 'Branch Admin']);
        $this->adminUser1->roles()->attach($adminRole);
        $this->adminUser1->unsetRelation('roles');

        // Branch 2
        $this->branch2 = Branch::create([
            'name' => 'Beşiktaş Şubesi',
            'code' => 'BSK-01',
            'slug' => 'bsk-01',
            'status' => 'active',
        ]);
        $this->adminUser2 = User::factory()->create([
            'branch_id' => $this->branch2->id,
        ]);
        $this->adminUser2->roles()->attach($adminRole);
        $this->adminUser2->unsetRelation('roles');

        // Teacher User (Unauthorized for settings)
        $this->teacherUser = User::factory()->create([
            'branch_id' => $this->branch1->id,
        ]);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $this->teacherUser->roles()->attach($teacherRole);
        $this->teacherUser->unsetRelation('roles');
    }

    public function test_admin_can_access_institution_settings_page(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->get(route('admin.settings.institution.index'));

        $response->assertStatus(200);
        $response->assertSee('Kurum Sistem Ayarları');
    }

    public function test_unauthorized_user_cannot_access_institution_settings(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->get(route('admin.settings.institution.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_update_general_settings(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.settings.institution.updateGeneral'), [
                'institution_name' => 'Kadıköy Çözüm Akademi',
                'description' => 'Gelişmiş YKS Hazırlık Kursu',
                'phone' => '02164445566',
                'email' => 'info@cozumakademi.com',
                'address' => 'Moda Cad. No:12 Kadıköy',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'website' => 'https://www.cozumakademi.com',
                'tax_number' => '9876543210',
                'invoice_title' => 'Kadıköy Çözüm Eğitim A.Ş.',
                'invoice_tax_office' => 'Rıhtım VD',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('institution_settings', [
            'branch_id' => $this->branch1->id,
            'institution_name' => 'Kadıköy Çözüm Akademi',
            'tax_number' => '9876543210',
        ]);
    }

    public function test_admin_can_update_branding_settings_with_file_upload(): void
    {
        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logo.png', 300, 100);
        $favicon = UploadedFile::fake()->image('favicon.png', 32, 32);

        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.settings.institution.updateBranding'), [
                'primary_color' => '#6366f1',
                'secondary_color' => '#1e293b',
                'logo' => $logo,
                'favicon' => $favicon,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $settings = InstitutionSetting::where('branch_id', $this->branch1->id)->first();

        $this->assertEquals('#6366f1', $settings->primary_color);
        $this->assertEquals('#1e293b', $settings->secondary_color);
        $this->assertNotNull($settings->logo);
        $this->assertNotNull($settings->favicon);

        Storage::disk('public')->assertExists($settings->logo);
        Storage::disk('public')->assertExists($settings->favicon);
    }

    public function test_logo_upload_rejects_invalid_file_type(): void
    {
        Storage::fake('public');

        $invalidFile = UploadedFile::fake()->create('malicious.php', 100, 'text/x-php');

        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.settings.institution.updateBranding'), [
                'logo' => $invalidFile,
            ]);

        $response->assertSessionHasErrors(['logo']);
    }

    public function test_admin_can_update_regional_settings(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.settings.institution.updateRegional'), [
                'language' => 'tr',
                'timezone' => 'Europe/Istanbul',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('institution_settings', [
            'branch_id' => $this->branch1->id,
            'language' => 'tr',
            'timezone' => 'Europe/Istanbul',
        ]);
    }

    public function test_admin_can_update_notification_preferences(): void
    {
        $response = $this->actingAs($this->adminUser1)
            ->post(route('admin.settings.institution.updateNotifications'), [
                'email_notifications' => '1',
                'system_notifications' => '1',
            ]);

        $response->assertRedirect();

        $settings = InstitutionSetting::where('branch_id', $this->branch1->id)->first();
        $this->assertTrue($settings->notification_preferences['email_notifications']);
        $this->assertTrue($settings->notification_preferences['system_notifications']);
        $this->assertFalse($settings->notification_preferences['parent_notifications']);
    }

    public function test_tenant_isolation_keeps_institution_settings_isolated_between_branches(): void
    {
        // Update Branch 1
        $this->settingService->updateSettings($this->branch1, [
            'institution_name' => 'Kadıköy Özel Dershanesi',
            'phone' => '02161111111',
        ]);

        // Update Branch 2
        $this->settingService->updateSettings($this->branch2, [
            'institution_name' => 'Beşiktaş Özel Dershanesi',
            'phone' => '02122222222',
        ]);

        $settings1 = $this->settingService->getSettings($this->branch1);
        $settings2 = $this->settingService->getSettings($this->branch2);

        $this->assertEquals('Kadıköy Özel Dershanesi', $settings1->institution_name);
        $this->assertEquals('Beşiktaş Özel Dershanesi', $settings2->institution_name);
        $this->assertNotEquals($settings1->institution_name, $settings2->institution_name);
    }
}
