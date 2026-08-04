<?php

namespace App\Domain\Institution\Services;

use App\Domain\Institution\Models\InstitutionSetting;
use App\Models\Branch;
use App\Core\Context\TenantContext;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class InstitutionSettingService
{
    /**
     * Get or initialize institution settings for a branch.
     */
    public function getSettings($branch = null): InstitutionSetting
    {
        $branchId = $this->resolveBranchId($branch);
        $branchObj = Branch::find($branchId);

        return InstitutionSetting::firstOrCreate(
            ['branch_id' => $branchId],
            [
                'institution_name' => $branchObj ? $branchObj->name : 'Dershane Kurumu',
                'phone' => '02120000000',
                'email' => 'info@dershane.com',
                'address' => 'Merkez Mah. Kurum Cad. No:1',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#0f172a',
                'timezone' => 'Europe/Istanbul',
                'language' => 'tr',
                'notification_preferences' => [
                    'email_notifications' => true,
                    'system_notifications' => true,
                    'parent_notifications' => true,
                ],
                'invoice_information' => [
                    'title' => $branchObj ? $branchObj->name : 'Dershane Kurumu',
                    'tax_office' => 'Kadıköy VD',
                    'tax_number' => '1234567890',
                ],
            ]
        );
    }

    /**
     * Update general institution settings.
     */
    public function updateSettings($branch, array $data): InstitutionSetting
    {
        $settings = $this->getSettings($branch);

        $fillableData = collect($data)->only([
            'institution_name',
            'description',
            'phone',
            'email',
            'address',
            'city',
            'district',
            'website',
            'tax_number',
            'invoice_information',
        ])->toArray();

        $settings->update($fillableData);

        return $settings->fresh();
    }

    /**
     * Update branding settings including logo and favicon file uploads.
     */
    public function updateBranding($branch, array $data, ?UploadedFile $logoFile = null, ?UploadedFile $faviconFile = null): InstitutionSetting
    {
        $settings = $this->getSettings($branch);
        $branchId = $settings->branch_id;

        $updateData = [];

        if (isset($data['primary_color'])) {
            $updateData['primary_color'] = $data['primary_color'];
        }

        if (isset($data['secondary_color'])) {
            $updateData['secondary_color'] = $data['secondary_color'];
        }

        if ($logoFile) {
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $logoPath = $logoFile->store("uploads/institution/{$branchId}/logos", 'public');
            $updateData['logo'] = $logoPath;
        }

        if ($faviconFile) {
            if ($settings->favicon && Storage::disk('public')->exists($settings->favicon)) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $faviconPath = $faviconFile->store("uploads/institution/{$branchId}/favicons", 'public');
            $updateData['favicon'] = $faviconPath;
        }

        $settings->update($updateData);

        return $settings->fresh();
    }

    /**
     * Update contact information settings.
     */
    public function updateContactInfo($branch, array $data): InstitutionSetting
    {
        $settings = $this->getSettings($branch);

        $settings->update(collect($data)->only([
            'phone',
            'email',
            'address',
            'city',
            'district',
            'website',
        ])->toArray());

        return $settings->fresh();
    }

    /**
     * Update regional settings (language, timezone).
     */
    public function updateRegionalSettings($branch, array $data): InstitutionSetting
    {
        $settings = $this->getSettings($branch);

        $settings->update(collect($data)->only([
            'language',
            'timezone',
        ])->toArray());

        return $settings->fresh();
    }

    /**
     * Update notification preferences.
     */
    public function updateNotificationPreferences($branch, array $data): InstitutionSetting
    {
        $settings = $this->getSettings($branch);

        $preferences = [
            'email_notifications' => !empty($data['email_notifications']),
            'system_notifications' => !empty($data['system_notifications']),
            'parent_notifications' => !empty($data['parent_notifications']),
        ];

        $settings->update(['notification_preferences' => $preferences]);

        return $settings->fresh();
    }

    /**
     * Resolve branch ID from input parameter, session, or active context.
     */
    protected function resolveBranchId($branch = null): int
    {
        if ($branch instanceof Branch) {
            return $branch->id;
        }

        if (is_numeric($branch) && $branch > 0) {
            return (int) $branch;
        }

        if (session('active_branch_id')) {
            return (int) session('active_branch_id');
        }

        if (auth()->check() && auth()->user()->branch_id) {
            return (int) auth()->user()->branch_id;
        }

        return (int) (TenantContext::getActiveBranchId() ?? 1);
    }
}
