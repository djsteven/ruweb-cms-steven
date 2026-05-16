<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, config('cms.roles', []));
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'locale'   => ['nullable', 'string', Rule::in(\App\Models\Locale::installedCodes())],
            'slug'     => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('menus', 'slug')->where('locale', $this->input('locale', $this->route('menu')->locale))->ignore($this->route('menu')->id)],
            'location' => ['nullable', 'string', 'max:255', Rule::unique('menus', 'location')->where('locale', $this->input('locale', $this->route('menu')->locale))->ignore($this->route('menu')->id)],
        ];
    }
}
