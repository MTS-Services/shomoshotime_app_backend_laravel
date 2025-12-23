<?php

namespace App\Http\Requests\API\V1\ChatManagement;

use Illuminate\Foundation\Http\FormRequest;

class ParticipentStoreRequest extends FormRequest
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
            'conversation_id'      => 'required|exists:conversations,id',
            'last_read_message_id' => 'nullable|exists:messages,id',
            'joined_at'            => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'sort_order.integer'           => 'Sort order must be a valid number.',
            'sort_order.min'               => 'Sort order cannot be negative.',
            
            'conversation_id.required'     => 'Conversation is required.',
            'conversation_id.exists'       => 'Selected conversation does not exist.',


            'last_read_message_id.exists'  => 'Selected message does not exist.',


        ];
    }
}
