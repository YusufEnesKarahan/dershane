<?php

namespace Tests\Feature;

use App\Domain\Notification\Services\AnnouncementCmsService;
use App\Models\AcademicTerm;
use App\Models\Announcement;
use App\Models\AnnouncementCategory;
use App\Models\Branch;
use App\Models\Notification;
use App\Models\Role;
use App\Models\SystemIdentity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnnouncementCmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $studentUser;
    protected Branch $branch1;
    protected Branch $branch2;
    protected AnnouncementCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        SystemIdentity::firstOrCreate(['company_name' => 'Test'], ['product_name' => 'Test ERP']);
        AcademicTerm::firstOrCreate(['name' => '2025-2026'], ['start_date' => now(), 'end_date' => now()->addYear(), 'is_active' => true]);

        $this->branch1 = Branch::create(['name' => 'Kadıköy Şube', 'slug' => 'kadikoy-' . uniqid()]);
        $this->branch2 = Branch::create(['name' => 'Beşiktaş Şube', 'slug' => 'besiktas-' . uniqid()]);

        $roleAdmin = Role::firstOrCreate(['name' => 'Super Admin'], ['guard_name' => 'web']);
        $roleStudent = Role::firstOrCreate(['name' => 'Student'], ['guard_name' => 'web']);

        $this->adminUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $this->adminUser->roles()->attach($roleAdmin);

        $this->studentUser = User::factory()->create(['branch_id' => $this->branch1->id]);
        $this->studentUser->roles()->attach($roleStudent);

        $this->category = AnnouncementCategory::create([
            'name' => 'Sınav',
            'slug' => 'sinav',
            'color' => 'blue',
            'icon' => 'fa-file-alt',
        ]);
    }

    public function test_announcement_can_be_created_with_category_and_attachments(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('sinav_takvimi.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->adminUser)->post(route('admin.announcements.store'), [
            'title' => '2026 YKS Deneme Sınavı Takvimi',
            'category_id' => $this->category->id,
            'summary' => 'Son deneme sınavı tarihleri açıklandı.',
            'content' => 'Detaylı sınav takvimi ektedir.',
            'status' => 'Published',
            'is_pinned' => '1',
            'is_popup' => '0',
            'is_all_branches' => '1',
            'attachments' => [$file],
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $response->assertSessionHas('success');

        $announcement = Announcement::where('title', '2026 YKS Deneme Sınavı Takvimi')->firstOrFail();
        $this->assertEquals($this->category->id, $announcement->category_id);
        $this->assertTrue($announcement->is_pinned);
        $this->assertCount(1, $announcement->attachments);
    }

    public function test_scope_published_and_schedule_filtering(): void
    {
        // Published & active
        $activeAnn = Announcement::create([
            'branch_id' => $this->branch1->id,
            'title' => 'Aktif Duyuru',
            'content' => 'İçerik',
            'status' => 'Published',
            'created_by' => $this->adminUser->id,
        ]);

        // Future publication date
        $futureAnn = Announcement::create([
            'branch_id' => $this->branch1->id,
            'title' => 'Gelecek Duyuru',
            'content' => 'İçerik',
            'status' => 'Published',
            'publish_at' => now()->addDays(5),
            'created_by' => $this->adminUser->id,
        ]);

        // Expired announcement
        $expiredAnn = Announcement::create([
            'branch_id' => $this->branch1->id,
            'title' => 'Süresi Dolmuş Duyuru',
            'content' => 'İçerik',
            'status' => 'Published',
            'expire_at' => now()->subDay(),
            'created_by' => $this->adminUser->id,
        ]);

        $publishedList = Announcement::published()->get();

        $this->assertTrue($publishedList->contains('id', $activeAnn->id));
        $this->assertFalse($publishedList->contains('id', $futureAnn->id));
        $this->assertFalse($publishedList->contains('id', $expiredAnn->id));
    }

    public function test_live_search_by_title_summary_content_and_category(): void
    {
        Announcement::create([
            'branch_id' => $this->branch1->id,
            'category_id' => $this->category->id,
            'title' => 'Özel Matematik Kampı',
            'summary' => 'Türev integraller kampa dahil.',
            'content' => 'Detaylı ders programı.',
            'status' => 'Published',
            'created_by' => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.announcements.index', ['search' => 'Matematik']));
        $response->assertOk();
        $response->assertSee('Özel Matematik Kampı');

        $responseCategorySearch = $this->actingAs($this->adminUser)->get(route('admin.announcements.index', ['search' => 'Sınav']));
        $responseCategorySearch->assertOk();
        $responseCategorySearch->assertSee('Özel Matematik Kampı');
    }

    public function test_popup_modal_session_tracking(): void
    {
        $popupAnn = Announcement::create([
            'branch_id' => $this->branch1->id,
            'title' => 'Acil Sistem Bakımı Duyurusu',
            'content' => 'Sistem bu gece bakımdadır.',
            'status' => 'Published',
            'is_popup' => true,
            'created_by' => $this->adminUser->id,
        ]);

        $service = new AnnouncementCmsService();
        $popupBefore = $service->getPopupForUser($this->adminUser);
        $this->assertNotNull($popupBefore);
        $this->assertEquals($popupAnn->id, $popupBefore->id);

        $response = $this->actingAs($this->adminUser)->post(route('admin.announcements.popup-seen', $popupAnn->id));
        $response->assertOk();

        $popupAfter = $service->getPopupForUser($this->adminUser);
        $this->assertNull($popupAfter);
    }

    public function test_publishing_announcement_dispatches_database_notifications_when_enabled(): void
    {
        $announcement = Announcement::create([
            'branch_id' => $this->branch1->id,
            'title' => 'Önemli Sınav Tarihi Değişikliği',
            'content' => 'Sınav gelecek haftaya ertelenmiştir.',
            'status' => 'Draft',
            'notify_roles' => ['Student'],
            'created_by' => $this->adminUser->id,
        ]);

        $service = new AnnouncementCmsService();
        $service->publish($announcement, true);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->studentUser->id,
            'type' => 'announcement',
        ]);
    }
}
