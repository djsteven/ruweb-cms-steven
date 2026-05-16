<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, config('cms.roles', []));
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
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn(\App\Models\Locale::catalogCodes()),
                Rule::unique('posts', 'slug')->where('locale', $this->input('locale', \App\Models\Locale::baseCode())),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:' . implode(',', config('cms.statuses'))],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'integer', 'exists:media,id'],
            'meta_json' => ['nullable', 'array'],
            'meta_json.description' => ['nullable', 'string', 'max:320'],
            'meta_json.og_title' => ['nullable', 'string', 'max:255'],
            'meta_json.og_description' => ['nullable', 'string', 'max:320'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', Rule::exists('taxonomies', 'id')->where('type', 'category')],
        ];
    }
}
