<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Hash;

class ChangePasswordAction
{
    public function execute($user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->save();
        $this->audit($user, 'password_change');
    }

    protected function audit($user, string $event)
    {
        // TODO: Hook to Activity Log
    }
}
