<?php

namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Session;

class AuthManager
{
    public function loadUserContext($user)
    {
        // Load roles, permissions, active edition/features into session/cache context
        // This sets up the Multi-Tenant/Edition readiness
        Session::put('user_roles', $user->roles->pluck('name')->toArray());
        
        // Edition and Feature initialization should happen here or via middleware.
        // The EditionManager handles the logic, we just trigger any necessary bootstrap.
    }
}
