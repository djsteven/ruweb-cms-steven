<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ValidatesStaleTranslationFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaxonomyRequest extends FormRequest
{
    use ValidatesStaleTranslationFields;

    public function authorize(): bool
    {
        return in_array($this->user()?->role, config('cms.roles', []));
    }

    public function rules(): array
    {
        $type     = $this->route('type');
        $taxonomy = $this->route('taxonomy');

        return [
            'name'        => ['required', 'string', 'max:255'],
            'locale'      => ['nullable', 'string', Rule::in(\App\Models\Locale::installedCodes())],
            'slug'        => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn(\App\Models\Locale::catalogCodes()),
                Rule::unique('taxonomies')->where('type', $type)->where('locale', $this->input('locale', $taxonomy->locale))->ignore($taxonomy->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id'   => ['nullable', 'integer', 'exists:taxonomies,id'],
            'order'       => ['nullable', 'integer', 'min:0'],
            'acknowledged_fields' => ['nullable', 'array'],
            'acknowledged_fields.*' => ['string'],
        ];
    }

    protected function staleTranslationEntity(): ?Model
    {
        return $this->route('taxonomy');
    }
}
