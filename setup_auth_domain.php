<?php

function createClass($path, $content) {
    $dir = dirname(__DIR__ . '/' . $path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(__DIR__ . '/' . $path, $content);
}

// 1. config/security.php
createClass('config/security.php', "<?php

return [
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'uncompromised' => false,
    ],
    'login' => [
        'max_attempts' => 5,
        'decay_minutes' => 1,
    ],
];
");

// 2. Domain/Auth/Services/AuthManager.php
createClass('app/Domain/Auth/Services/AuthManager.php', "<?php

namespace App\Domain\Auth\Services;

use Illuminate\Support\Facades\Session;

class AuthManager
{
    public function loadUserContext(\$user)
    {
        // Load roles, permissions, active edition/features into session/cache context
        // This sets up the Multi-Tenant/Edition readiness
        Session::put('user_roles', \$user->roles->pluck('name')->toArray());
        
        // Edition and Feature initialization should happen here or via middleware.
        // The EditionManager handles the logic, we just trigger any necessary bootstrap.
    }
}
");

// 3. Actions
$actions = [
    'LoginAction' => "<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use App\Domain\Auth\Services\AuthManager;

class LoginAction
{
    public function __construct(protected AuthManager \$authManager) {}

    public function execute(array \$credentials, bool \$remember = false): bool
    {
        if (Auth::attempt(\$credentials, \$remember)) {
            session()->regenerate();
            \$this->authManager->loadUserContext(Auth::user());
            \$this->audit(Auth::user(), 'login');
            return true;
        }

        \$this->audit(null, 'failed_login', ['email' => \$credentials['email'] ?? null]);
        return false;
    }

    protected function audit(\$user, string \$event, array \$metadata = [])
    {
        // TODO: Hook to Activity Log
    }
}
",
    'LogoutAction' => "<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    public function execute(): void
    {
        \$user = Auth::user();
        if (\$user) {
            \$this->audit(\$user, 'logout');
        }
        
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    protected function audit(\$user, string \$event)
    {
        // TODO: Hook to Activity Log
    }
}
",
    'ForgotPasswordAction' => "<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Password;

class ForgotPasswordAction
{
    public function execute(array \$data): string
    {
        // Dispatches the reset link
        return Password::sendResetLink(\$data);
    }
}
",
    'ResetPasswordAction' => "<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordAction
{
    public function execute(array \$data): string
    {
        return Password::reset(\$data, function (\$user, \$password) {
            \$user->password = Hash::make(\$password);
            \$user->setRememberToken(Str::random(60));
            \$user->save();
        });
    }
}
",
    'ChangePasswordAction' => "<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Hash;

class ChangePasswordAction
{
    public function execute(\$user, string \$newPassword): void
    {
        \$user->password = Hash::make(\$newPassword);
        \$user->save();
        \$this->audit(\$user, 'password_change');
    }

    protected function audit(\$user, string \$event)
    {
        // TODO: Hook to Activity Log
    }
}
"
];

foreach ($actions as $name => $content) {
    createClass('app/Domain/Auth/Actions/' . $name . '.php', $content);
}

echo "Auth Domain created.\n";
