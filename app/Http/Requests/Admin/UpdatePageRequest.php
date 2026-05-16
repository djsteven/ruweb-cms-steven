<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\ValidatesStaleTranslationFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    use ValidatesStaleTranslationFields;

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
                Rule::unique('pages', 'slug')
                    ->where('locale', $this->input('locale', $this->route('page')->locale))
                    ->ignore($this->route('page')->id),
            ],
            'template_key' => ['required', 'string', 'in:' . implode(',', array_keys(config('cms.templates')))],
            'status' => ['required', 'string', 'in:' . implode(',', config('cms.statuses'))],
            'featured_image' => ['nullable', 'integer', 'exists:media,id'],
            'content_json' => ['nullable', 'array'],
            'content_json.meta' => ['nullable', 'array'],
            'content_json.sections' => ['nullable', 'array'],
            'acknowledged_fields' => ['nullable', 'array'],
            'acknowledged_fields.*' => ['string'],
        ];
    }

    protected function staleTranslationEntity(): ?Model
    {
        return $this->route('page');
    }
}
