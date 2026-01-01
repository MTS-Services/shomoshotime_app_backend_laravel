<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;

class ContentRequest extends BaseRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'type' => 'required|integer|in:0,1',
             'file'     => $this->isMethod('post')
                ? 'required|file|mimes:pdf,mp3,wav,ogg|max:10240'
                : 'sometimes|file|mimes:pdf,mp3,wav,ogg|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload a file.',
            'file.file' => 'The uploaded item must be a valid file.',
            'file.mimes' => 'Only PDF and audio files (mp3, wav, ogg) are allowed.',
            'file.max' => 'The file size must not exceed 10MB.',
        ];
    }
}
