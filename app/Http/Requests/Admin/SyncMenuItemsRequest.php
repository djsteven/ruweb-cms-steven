<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SyncMenuItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, config('cms.roles', []));
    }

    public function rules(): array
    {
        return [
            'items'               => ['present', 'array'],
            'items.*.id'          => ['nullable', 'integer'],
            'items.*.parent_id'   => ['nullable', 'integer'],
            'items.*.label'       => ['required', 'string', 'max:255'],
            'items.*.type'        => ['required', 'string', 'in:custom_link,page,post,taxonomy'],
            'items.*.linkable_id' => ['nullable', 'integer'],
            'items.*.url'         => ['nullable', 'string', 'max:2048'],
            'items.*.target'      => ['required', 'string', 'in:_self,_blank'],
            'items.*.order'       => ['required', 'integer', 'min:0'],
        ];
    }
}
