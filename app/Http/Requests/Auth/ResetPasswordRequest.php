<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
        if (config('security.password.uncompromised')) $passwordRule->uncompromised();

        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', $passwordRule],
        ];
    }
}
