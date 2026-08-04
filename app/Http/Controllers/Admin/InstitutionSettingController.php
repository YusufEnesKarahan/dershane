<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Institution\Services\InstitutionSettingService;

class InstitutionSettingController extends Controller
{
    public function __construct(
        protected InstitutionSettingService $settingService
    ) {}

    /**
     * Display institution settings dashboard with tabs.
     */
    public function index()
    {
        $settings = $this->settingService->getSettings();
        $this->authorize('view', $settings);

        return view('admin.settings.institution.index', compact('settings'));
    }

    /**
     * Update general & contact information settings.
     */
    public function updateGeneral(Request $request)
    {
        $settings = $this->settingService->getSettings();
        $this->authorize('update', $settings);

        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'tax_number' => 'nullable|string|max:50',
            'invoice_title' => 'nullable|string|max:255',
            'invoice_tax_office' => 'nullable|string|max:100',
        ]);

        $validated['invoice_information'] = [
            'title' => $request->input('invoice_title', $settings->institution_name),
            'tax_office' => $request->input('invoice_tax_office', ''),
            'tax_number' => $request->input('tax_number', ''),
        ];

        $this->settingService->updateSettings(null, $validated);

        return redirect()->route('admin.settings.institution.index', ['tab' => 'general'])
            ->with('success', 'Genel kurum bilgileri başarıyla güncellendi.');
    }

    /**
     * Update branding, logo and favicon uploads.
     */
    public function updateBranding(Request $request)
    {
        $settings = $this->settingService->getSettings();
        $this->authorize('update', $settings);

        $request->validate([
            'primary_color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}){1,2}$/'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{3}){1,2}$/'],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:jpeg,png,jpg,svg,webp,ico|max:2048',
        ]);

        $this->settingService->updateBranding(
            null,
            $request->only(['primary_color', 'secondary_color']),
            $request->file('logo'),
            $request->file('favicon')
        );

        return redirect()->route('admin.settings.institution.index', ['tab' => 'branding'])
            ->with('success', 'Marka ve görsel ayarlarınız başarıyla güncellendi.');
    }

    /**
     * Update regional settings (language, timezone).
     */
    public function updateRegional(Request $request)
    {
        $settings = $this->settingService->getSettings();
        $this->authorize('update', $settings);

        $validated = $request->validate([
            'language' => 'required|string|in:tr,en',
            'timezone' => 'required|string|max:100',
        ]);

        $this->settingService->updateRegionalSettings(null, $validated);

        return redirect()->route('admin.settings.institution.index', ['tab' => 'regional'])
            ->with('success', 'Bölgesel tercihler başarıyla güncellendi.');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $settings = $this->settingService->getSettings();
        $this->authorize('update', $settings);

        $this->settingService->updateNotificationPreferences(null, [
            'email_notifications' => $request->has('email_notifications'),
            'system_notifications' => $request->has('system_notifications'),
            'parent_notifications' => $request->has('parent_notifications'),
        ]);

        return redirect()->route('admin.settings.institution.index', ['tab' => 'notifications'])
            ->with('success', 'Bildirim tercihleri başarıyla güncellendi.');
    }
}
