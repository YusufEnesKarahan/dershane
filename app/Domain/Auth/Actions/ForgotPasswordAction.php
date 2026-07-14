<?php
namespace App\Domain\Auth\Actions;

use Illuminate\Support\Facades\Password;

class ForgotPasswordAction
{
    public function execute(array $data): string
    {
        // Dispatches the reset link
        return Password::sendResetLink($data);
    }
}
