<?php
namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('roles.update');
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id;
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $roleId],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }
}
