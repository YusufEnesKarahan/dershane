<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingGroup;
use App\Models\PlatformSetting;
use App\DTOs\Platform\UpdateSettingDTO;
use App\Domain\Platform\Actions\UpdateSettingAction;
use App\Domain\Platform\Actions\ResetSettingsAction;
use App\Domain\Platform\Actions\ExportSettingsAction;
use App\Domain\Platform\Actions\ImportSettingsAction;
use App\Domain\Platform\Services\MailConfigurationService;
use App\Domain\Platform\Services\StorageConfigurationService;
use App\Domain\Platform\Services\ThemeService;
use Illuminate\Http\Request;

class PlatformSettingController extends Controller
{
    public function __construct(
        protected MailConfigurationService $mailService,
        protected StorageConfigurationService $storageService,
        protected ThemeService $themeService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', PlatformSetting::class);

        $groups = SettingGroup::with('settings')->orderBy('sort_order')->get();

        return view('admin.settings.index', compact('groups'));
    }

    public function update(Request $request, UpdateSettingAction $action)
    {
        $this->authorize('update', PlatformSetting::class);

        // Pre-Validation
        $request->validate([
            'settings' => 'required|array',
        ]);

        $dto = UpdateSettingDTO::fromRequest($request->all());
        
        $action->execute($dto);

        // Recompile dynamic colors variables to theme_custom.css stylesheet
        $this->themeService->compileThemeCss();

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }

    public function testMail()
    {
        $this->authorize('update', PlatformSetting::class);

        $success = $this->mailService->testConnection();

        if ($success) {
            return response()->json(['success' => true, 'message' => 'SMTP connection established successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'SMTP connection failed. Check host/port parameters.'], 400);
    }

    public function testStorage()
    {
        $this->authorize('update', PlatformSetting::class);

        $success = $this->storageService->testStorageDisk();

        if ($success) {
            return response()->json(['success' => true, 'message' => 'Storage disk settings verified successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid disk selection.'], 400);
    }

    public function export(Request $request, ExportSettingsAction $action)
    {
        $this->authorize('update', PlatformSetting::class);

        $encrypt = $request->has('encrypt');
        $json = $action->execute($encrypt);

        return response($json)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="settings_backup.json"');
    }

    public function import(Request $request, ImportSettingsAction $action)
    {
        $this->authorize('update', PlatformSetting::class);

        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $file = $request->file('backup_file');
        $content = file_get_contents($file->getRealPath());
        
        $isEncrypted = $request->has('is_encrypted');

        $success = $action->execute($content, $isEncrypted);

        if ($success) {
            return redirect()->route('admin.settings.index')->with('success', 'Settings backup imported successfully.');
        }

        return redirect()->route('admin.settings.index')->with('error', 'Import failed. Invalid file payload.');
    }

    public function reset(ResetSettingsAction $action)
    {
        $this->authorize('update', PlatformSetting::class);

        $action->execute();

        return redirect()->route('admin.settings.index')->with('success', 'All settings reset to default values.');
    }
}
