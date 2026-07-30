<?php

namespace App\Http\Controllers\Admin\HQ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HQRole;
use App\Models\HQPermission;
use App\Models\HQApiKey;
use App\Models\HQServiceAccount;
use App\Models\HQSecuritySession;
use App\Models\HQAuditLog;

class HQIdentityController extends Controller
{
    public function overview()
    {
        return view('admin.hq.identity.overview', [
            'totalUsers' => User::count(),
            'totalRoles' => HQRole::count(),
            'totalApiKeys' => HQApiKey::where('is_revoked', false)->count(),
            'activeSessions' => HQSecuritySession::where('is_active', true)->count(),
        ]);
    }

    public function users()
    {
        $users = User::with('roles', 'tenant')->paginate(20);
        return view('admin.hq.identity.users', compact('users'));
    }

    public function roles()
    {
        $roles = HQRole::withCount('users', 'permissions')->get();
        return view('admin.hq.identity.roles', compact('roles'));
    }

    public function permissions()
    {
        $permissions = HQPermission::withCount('roles')->get();
        return view('admin.hq.identity.permissions', compact('permissions'));
    }

    public function apiKeys()
    {
        $keys = HQApiKey::with('user', 'tenant')->latest()->paginate(20);
        return view('admin.hq.identity.api-keys', compact('keys'));
    }

    public function serviceAccounts()
    {
        $accounts = HQServiceAccount::with('tenant')->latest()->paginate(20);
        return view('admin.hq.identity.service-accounts', compact('accounts'));
    }

    public function sessions()
    {
        $sessions = HQSecuritySession::with('user')->where('is_active', true)->latest('last_activity')->paginate(20);
        return view('admin.hq.identity.sessions', compact('sessions'));
    }

    public function securityLogs()
    {
        $logs = HQAuditLog::where('category', 'iam')
            ->orWhere('category', 'security')
            ->latest()
            ->paginate(30);
        return view('admin.hq.identity.security-logs', compact('logs'));
    }
}
