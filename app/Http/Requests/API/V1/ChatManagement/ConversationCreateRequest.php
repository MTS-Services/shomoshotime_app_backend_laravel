<?php

namespace App\Http\Requests\API\V1\ChatManagement;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ConversationCreateRequest extends FormRequest
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

            'type'             => 'required|integer|in:' . Conversation::TYPE_PRIVATE . ',' . Conversation::TYPE_GROUP,
            'name'             => 'required|string|max:255',
            'last_message_at'  => 'required|date',
        ];
    }
}
