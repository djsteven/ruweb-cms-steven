<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'locale' => ['nullable', 'string', Rule::in(\App\Models\Locale::installedCodes())],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::notIn(\App\Models\Locale::catalogCodes()),
                Rule::unique('pages', 'slug')->where('locale', $this->input('locale', \App\Models\Locale::baseCode())),
            ],
            'template_key' => ['required', 'string', 'in:' . implode(',', array_keys(config('cms.templates')))],
            'status' => ['required', 'string', 'in:' . implode(',', config('cms.statuses'))],
            'featured_image' => ['nullable', 'integer', 'exists:media,id'],
            'content_json' => ['nullable', 'array'],
            'content_json.meta' => ['nullable', 'array'],
            'content_json.meta.title' => ['nullable', 'string', 'max:255'],
            'content_json.meta.description' => ['nullable', 'string', 'max:320'],
            'content_json.sections' => ['nullable', 'array'],
        ];
    }
}
