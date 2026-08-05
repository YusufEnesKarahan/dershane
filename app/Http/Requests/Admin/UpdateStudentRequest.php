<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Core\Context\TenantContext;
use App\Models\Branch;
use App\Models\Student;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');
        return auth()->user()?->can('update', $student) ?? true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone') && $this->phone) {
            $cleaned = preg_replace('/[^0-9]/', '', $this->phone);
            if (str_starts_with($cleaned, '90') && strlen($cleaned) === 12) {
                $cleaned = substr($cleaned, 2);
            } elseif (str_starts_with($cleaned, '0') && strlen($cleaned) === 11) {
                $cleaned = substr($cleaned, 1);
            }
            $this->merge(['phone_cleaned' => $cleaned]);
        }

        if ($this->has('guardian_phone') && $this->guardian_phone) {
            $gCleaned = preg_replace('/[^0-9]/', '', $this->guardian_phone);
            if (str_starts_with($gCleaned, '90') && strlen($gCleaned) === 12) {
                $gCleaned = substr($gCleaned, 2);
            } elseif (str_starts_with($gCleaned, '0') && strlen($gCleaned) === 11) {
                $gCleaned = substr($gCleaned, 1);
            }
            $this->merge(['guardian_phone_cleaned' => $gCleaned]);
        }
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = is_object($student) ? $student->id : $student;

        $branchId = TenantContext::getActiveBranchId()
            ?? session('active_branch_id')
            ?? auth()->user()?->branch_id
            ?? Branch::value('id')
            ?? 1;

        return [
            'student_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'student_number')->where('branch_id', $branchId)->ignore($studentId),
            ],
            'identity_number' => [
                'nullable',
                'string',
                'size:11',
                'regex:/^[1-9][0-9]{10}$/',
                Rule::unique('students', 'identity_number')->ignore($studentId),
            ],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', Rule::in(['Kadın', 'Erkek', 'Female', 'Male'])],
            'birth_date' => ['nullable', 'date'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'status' => ['nullable', 'string', Rule::in(['Active', 'Inactive', 'Graduated', 'Suspended'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'phone_cleaned' => ['nullable', 'string', 'regex:/^5[0-9]{9}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address_text' => ['nullable', 'string'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'guardian_phone_cleaned' => ['nullable', 'string', 'regex:/^5[0-9]{9}$/'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
