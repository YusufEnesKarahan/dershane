<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Core\Context\TenantContext;
use App\Models\Branch;
use App\Models\Student;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', Student::class) ?? true;
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

        $this->merge([
            'create_user_account' => filter_var($this->input('create_user_account'), FILTER_VALIDATE_BOOLEAN),
            'create_parent_account' => filter_var($this->input('create_parent_account'), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        $branchId = TenantContext::getActiveBranchId()
            ?? session('active_branch_id')
            ?? auth()->user()?->branch_id
            ?? Branch::value('id')
            ?? 1;

        return [
            'student_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'student_number')->where('branch_id', $branchId),
            ],
            'identity_number' => [
                'nullable',
                'string',
                'size:11',
                'regex:/^[1-9][0-9]{10}$/',
                Rule::unique('students', 'identity_number')->whereNotNull('identity_number'),
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
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

            // Student User Account Toggle & Fields
            'create_user_account' => ['nullable', 'boolean'],
            'user_email' => [
                'required_if:create_user_account,true,1',
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'user_password' => [
                'required_if:create_user_account,true,1',
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            // Parent Account Toggle & Fields
            'create_parent_account' => ['nullable', 'boolean'],
            'guardian_name' => ['required_if:create_parent_account,true,1', 'nullable', 'string', 'max:255'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'guardian_phone_cleaned' => ['nullable', 'string', 'regex:/^5[0-9]{9}$/'],
            'guardian_email' => [
                'required_if:create_parent_account,true,1',
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'guardian_password' => [
                'required_if:create_parent_account,true,1',
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'student_number.required' => 'Öğrenci numarası zorunludur.',
            'student_number.unique' => 'Bu öğrenci numarası bu şubede zaten kayıtlı.',
            'identity_number.size' => 'T.C. Kimlik Numarası 11 haneli olmalıdır.',
            'identity_number.regex' => 'Geçerli bir T.C. Kimlik Numarası giriniz.',
            'identity_number.unique' => 'Bu T.C. Kimlik Numarası ile kayıtlı başka bir öğrenci var.',
            'first_name.required' => 'Öğrenci adı zorunludur.',
            'last_name.required' => 'Öğrenci soyadı zorunludur.',
            'gender.in' => 'Cinsiyet yalnızca Kadın veya Erkek seçilebilir.',
            'phone_cleaned.regex' => 'Telefon numarası geçerli bir Türkiye cep telefonu olmalıdır (Örn: 5XX XXX XX XX).',
            'guardian_phone_cleaned.regex' => 'Veli telefon numarası geçerli bir Türkiye cep telefonu olmalıdır (Örn: 5XX XXX XX XX).',
            'user_email.required_if' => 'Öğrenci kullanıcı hesabı için e-posta adresi zorunludur.',
            'user_email.unique' => 'Bu e-posta adresi ile kayıtlı bir kullanıcı zaten var.',
            'user_password.required_if' => 'Öğrenci kullanıcı hesabı için şifre zorunludur.',
            'user_password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'user_password.confirmed' => 'Girilen şifreler eşleşmiyor.',
            'guardian_email.required_if' => 'Veli kullanıcı hesabı oluşturulurken veli e-postası zorunludur.',
            'guardian_email.unique' => 'Veli e-posta adresi başka bir kullanıcı tarafından kullanılıyor.',
            'guardian_password.required_if' => 'Veli kullanıcı hesabı oluşturulurken şifre zorunludur.',
        ];
    }
}
