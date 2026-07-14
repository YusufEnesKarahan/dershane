<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    public function execute(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->audit($user, 'logout');
        }
        
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    protected function audit($user, string $event)
    {
        // TODO: Hook to Activity Log
    }
}
