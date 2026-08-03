<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Auth;
use App\Domain\Auth\Services\AuthManager;

class LoginAction
{
    public function __construct(protected AuthManager $authManager) {}

    public function execute(array $credentials, bool $remember = false): bool
    {
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                $this->audit($user, 'failed_login_inactive');
                return false;
            }

            session()->regenerate();
            $this->authManager->loadUserContext($user);
            $this->audit($user, 'login');
            return true;
        }

        $this->audit(null, 'failed_login', ['email' => $credentials['email'] ?? null]);
        return false;
    }

    protected function audit($user, string $event, array $metadata = [])
    {
        // TODO: Hook to Activity Log
    }
}
