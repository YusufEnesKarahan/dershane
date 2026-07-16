<?php
namespace App\Http\Requests\Admin\CMS;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\PageStatus;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('pages.update');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'template' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:pages,id'],
            'sort_order' => ['nullable', 'integer'],
            'is_homepage' => ['nullable', 'boolean'],
            'seo' => ['nullable', 'array'],
            'status' => ['nullable', new Enum(PageStatus::class)],
        ];
    }
}
