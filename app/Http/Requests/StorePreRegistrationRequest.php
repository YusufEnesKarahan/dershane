<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'classroom_name' => 'nullable|string|max:100',
            'interested_program' => 'nullable|string|max:255',
            'source' => 'required|string|in:Instagram,Google,Referans,Web,Telefon,Diğer',
            'status' => 'required|string|in:Yeni,Arandı,Randevu,Kayıt Oldu,İptal',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'reminder_at' => 'nullable|date',
        ];
    }
}
