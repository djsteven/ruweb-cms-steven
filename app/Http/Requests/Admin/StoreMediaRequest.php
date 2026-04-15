<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageMaxKb = config('cms.upload.image_max_size');
        $docMaxKb = config('cms.upload.document_max_size');
        $maxKb = max($imageMaxKb, $docMaxKb);
        $allowedMimes = implode(',', array_merge(
            config('cms.upload.allowed_image_mimes'),
            config('cms.upload.allowed_document_mimes')
        ));

        return [
            'file' => [
                'required_without:files',
                'nullable',
                'file',
                "max:{$maxKb}",
                "mimetypes:{$allowedMimes}",
            ],
            'files' => ['required_without:file', 'nullable', 'array', 'min:1'],
            'files.*' => [
                'file',
                "max:{$maxKb}",
                "mimetypes:{$allowedMimes}",
            ],
            'alt' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimetypes' => __('admin.validation_file_mimetypes'),
            'file.max' => __('admin.validation_file_max'),
            'file.uploaded' => __('admin.validation_file_uploaded'),
            'files.*.mimetypes' => __('admin.validation_file_mimetypes'),
            'files.*.max' => __('admin.validation_file_max'),
            'files.*.uploaded' => __('admin.validation_file_uploaded'),
        ];
    }
}
