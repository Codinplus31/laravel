<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow anyone to upload
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'files' => 'required|array|min:1|max:5',
            'files.*' => 'required|file|max:102400|mimes:jpg,png,pdf,docx,zip', // 100MB max
            'expires_in' => 'nullable|integer|min:1|max:30',
            'email_to_notify' => 'nullable|email',
            'password' => 'nullable|string|min:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Please select at least one file to upload.',
            'files.max' => 'You can upload a maximum of 5 files at once.',
            'files.*.max' => 'Each file must not exceed 100MB.',
            'files.*.mimes' => 'Only jpg, png, pdf, docx, and zip files are allowed.',
        ];
    }
}