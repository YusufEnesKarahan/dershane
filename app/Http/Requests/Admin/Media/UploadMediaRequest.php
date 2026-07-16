<?php
namespace App\Http\Requests\Admin\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('media.create');
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:' . config('media.max_upload_size', 10240)],
            'folder_id' => ['nullable', 'exists:media_folders,id'],
            'collection' => ['nullable', 'string'],
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
