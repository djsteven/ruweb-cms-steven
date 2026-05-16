<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaxonomyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, config('cms.roles', []));
    }

    public function rules(): array
    {
        $type = $this->route('type');

        return [
            'name'        => ['required', 'string', 'max:255'],
            'locale'      => ['nullable', 'string', Rule::in(\App\Models\Locale::installedCodes())],
            'slug'        => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn(\App\Models\Locale::catalogCodes()),
                Rule::unique('taxonomies')->where('type', $type)->where('locale', $this->input('locale', \App\Models\Locale::baseCode())),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id'   => ['nullable', 'integer', 'exists:taxonomies,id'],
            'order'       => ['nullable', 'integer', 'min:0'],
        ];
    }
}
