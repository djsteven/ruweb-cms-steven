<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'locale'   => ['nullable', 'string', Rule::in(\App\Models\Locale::installedCodes())],
            'slug'     => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('menus', 'slug')->where('locale', $this->input('locale', \App\Models\Locale::baseCode()))],
            'location' => ['nullable', 'string', 'max:255', Rule::unique('menus', 'location')->where('locale', $this->input('locale', \App\Models\Locale::baseCode()))],
        ];
    }
}
