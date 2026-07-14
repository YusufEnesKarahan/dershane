<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $passwordRule = Password::min(config('security.password.min_length', 8));
        if (config('security.password.require_uppercase')) $passwordRule->mixedCase();
        if (config('security.password.require_numbers')) $passwordRule->numbers();
        if (config('security.password.require_symbols')) $passwordRule->symbols();

        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', $passwordRule],
        ];
    }
}
