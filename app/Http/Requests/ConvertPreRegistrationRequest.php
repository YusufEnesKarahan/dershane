<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvertPreRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_number' => 'nullable|string|max:50',
            'tc_no' => 'nullable|string|max:20',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:30',
            'guardian_email' => 'nullable|email|max:255',
            'tuition_amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ];
    }
}
