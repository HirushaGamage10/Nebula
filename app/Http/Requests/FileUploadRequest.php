<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $category = $this->input('category', 'all');
        
        return [
            'file' => [
                'required',
                'file',
                'max:' . $this->getMaxSize($category),
                'mimes:' . $this->getAllowedExtensions($category),
            ],
            'category' => 'nullable|string|in:images,documents,certificates,photos,all',
            'directory' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\/_-]+$/',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The uploaded file is invalid.',
            'file.max' => 'The file size must not exceed :max kilobytes.',
            'file.mimes' => 'The file must be of type: :values.',
            'category.in' => 'Invalid file category selected.',
            'directory.regex' => 'Directory name contains invalid characters.',
        ];
    }

    /**
     * Get custom validation attributes.
     */
    public function attributes(): array
    {
        return [
            'file' => 'uploaded file',
            'category' => 'file category',
            'directory' => 'upload directory',
        ];
    }

    /**
     * Get maximum file size for category
     */
    private function getMaxSize(string $category): int
    {
        $sizes = [
            'images' => 2048, // 2MB
            'documents' => 5120, // 5MB
            'certificates' => 5120, // 5MB
            'photos' => 2048, // 2MB
            'all' => 10240, // 10MB
        ];

        return $sizes[$category] ?? $sizes['all'];
    }

    /**
     * Get allowed extensions for category
     */
    private function getAllowedExtensions(string $category): string
    {
        $extensions = [
            'images' => 'jpg,jpeg,png,gif,webp',
            'documents' => 'pdf,doc,docx,txt',
            'certificates' => 'pdf,jpg,jpeg,png',
            'photos' => 'jpg,jpeg,png',
            'all' => 'jpg,jpeg,png,gif,webp,pdf,doc,docx,txt',
        ];

        return $extensions[$category] ?? $extensions['all'];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
