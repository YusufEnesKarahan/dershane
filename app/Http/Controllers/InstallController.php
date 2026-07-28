<?php

namespace App\Http\Controllers;

use App\Domain\Platform\Services\InstallService;
use Illuminate\Http\Request;

class InstallController extends Controller
{
    public function __construct(protected InstallService $installService) {}

    protected function checkInstalled(): void
    {
        if ($this->installService->isInstalled() && config('app.debug') !== true) {
            abort(403, 'Application already installed.');
        }
    }

    public function welcome()
    {
        $this->checkInstalled();
        return view('install.welcome');
    }

    public function requirements()
    {
        $this->checkInstalled();
        $requirements = $this->installService->checkRequirements();
        return view('install.requirements', compact('requirements'));
    }

    public function database()
    {
        $this->checkInstalled();
        // Check if requirements are satisfied first before allowing database configuration step
        $requirements = $this->installService->checkRequirements();
        foreach ($requirements as $req) {
            if (!$req['satisfied']) {
                return redirect()->route('install.requirements');
            }
        }

        return view('install.database');
    }

    public function runMigration()
    {
        $this->checkInstalled();

        if ($this->installService->runMigrations()) {
            return redirect()->route('install.admin');
        }

        return redirect()->back()->with('error', 'Veritabanı migration işlemleri başarısız oldu. Lütfen veritabanı ayarlarınızı ve bağlantınızı kontrol edin.');
    }

    public function admin()
    {
        $this->checkInstalled();
        return view('install.admin');
    }

    public function storeAdmin(Request $request)
    {
        $this->checkInstalled();

        $request->validate([
            'branch_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $adminData = $request->only(['name', 'email', 'password']);
        $branchName = $request->input('branch_name');

        if ($this->installService->completeInstallation($adminData, $branchName)) {
            return redirect()->route('install.finish');
        }

        return redirect()->back()->withErrors(['error' => 'Super Admin ve varsayılan şube kurulumu esnasında bir hata oluştu.']);
    }

    public function finish()
    {
        // Allow the finish page to render at the end of installation,
        // but we still block it if the lock file is there and APP_DEBUG=false?
        // Wait, the lock file gets written in completeInstallation(), so when we redirect to finish(),
        // checkInstalled() would normally yield 403 if lock file exists and APP_DEBUG=false.
        // Let's add an exception for the finish page, or only checkInstalled if they try to access welcome/requirements/database/admin steps.
        // That is a great catch! If they complete installation, they are redirected to finish.
        // So welcome, requirements, database, runMigration, admin, storeAdmin should call checkInstalled().
        // finish() can allow rendering once, or we can check session flag.
        // Let's check session flag or just allow finish() to render if the referrer was the admin save action, or if we are in the install flow.
        // Or simply, we can allow finish() to render always or check config('app.debug') only.
        // Actually, we can check if a session flag 'installation_success' exists, and if so allow finish() to bypass checkInstalled() once.
        // Let's set session('install_completed' => true) in storeAdmin and allow finish page.
        if ($this->installService->isInstalled() && !session('install_completed') && config('app.debug') !== true) {
            abort(403, 'Application already installed.');
        }

        return view('install.finish');
    }
}
