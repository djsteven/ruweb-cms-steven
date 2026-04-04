<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug,' . $this->route('page')->id],
            'template_key' => ['required', 'string', 'in:' . implode(',', array_keys(config('cms.templates')))],
            'status' => ['required', 'string', 'in:' . implode(',', config('cms.statuses'))],
            'featured_image' => ['nullable', 'integer', 'exists:media,id'],
            'content_json' => ['nullable', 'array'],
            'content_json.meta' => ['nullable', 'array'],
            'content_json.sections' => ['nullable', 'array'],
        ];
    }
}
