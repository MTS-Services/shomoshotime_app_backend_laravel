<?php

namespace App\Http\Requests\API\V1\ChatManagement;

use Illuminate\Foundation\Http\FormRequest;

class MessageCreateRequest extends FormRequest
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
            'conversation_id' => 'required|exists:conversations,id',
            'message_content' => 'required|string',
            'send_at'         => 'required|date',
            'type'            => 'required|in:1,2,3,4,5',
            'status'          => 'nullable|in:1,2,3,4',
            'files' => [
                'nullable',
                'array',
                'max:11'
            ],
            // 👇 THE FIX IS HERE 👇
            'files.*' => [
                'nullable',
                'file',
                // Change 'mimes:' to 'mimetypes:'
                'mimetypes:' .
                    'image/jpeg,image/png,image/gif,image/bmp,image/svg+xml,image/webp,' .
                    'video/mp4,video/mov,video/avi,video/mpeg,video/quicktime,video/webm,video/x-matroska,' .
                    'audio/mpeg,audio/wav,audio/ogg,audio/aac,audio/webm,' .
                    'string' . 'application/pdf,application/msword,application/openxmlformats-officedocument.wordprocessingml.document,',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'conversation_id.required' => 'Conversation ID is required.',
            'conversation_id.exists'   => 'The selected conversation does not exist.',

            'sender_id.required' => 'Sender ID is required.',
            'sender_id.exists'   => 'The selected sender does not exist.',

            'message_content.required' => 'Message content cannot be empty.',
            'message_content.string'   => 'Message content must be text.',

            'send_at.required' => 'Send time is required.',
            'send_at.date'     => 'Send time must be a valid date.',

            // 👇 THE FIX IS HERE 👇
            // Change 'message_type' to 'type' to match the rule name
            'type.required' => 'Message type is required.',
            'type.in'       => 'Message type must be one of: 1=text, 2=image, 3=audio, 4=video, 5=file.',

            'files.array' => __('validation.property.files.array'),
            'files.max' => __('validation.property.files.max'),
            'files.*.file' => __('validation.property.files.star.file'),
            'files.*.mimetypes' => __('validation.property.files.star.mimes'), // Also good to update this to mimetypes

            'status.in' => 'Status must be one of: 1=sent, 2=delivered, 3=read, 4=failed.',
        ];
    }
}
