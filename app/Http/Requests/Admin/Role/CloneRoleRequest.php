<?php
namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class CloneRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('roles.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles', 'not_in:Administrator,Super Admin'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
        ];
    }
}
