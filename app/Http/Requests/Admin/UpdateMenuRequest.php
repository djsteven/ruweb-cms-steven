<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

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
            'slug'     => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:menus,slug,' . $this->route('menu')->id],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
