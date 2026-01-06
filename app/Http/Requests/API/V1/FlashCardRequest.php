<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;

class FlashCardRequest extends BaseRequest
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
        return [
            'content_id' => 'sometimes|required|integer|exists:contents,id',
            'question'   => 'sometimes|required|string|max:1000',
            'answer'     => 'sometimes|required|string|max:1000',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'content_id.required' => 'Content ID is required.',
            'content_id.integer'  => 'Content ID must be a valid number.',
            'content_id.exists'   => 'The specified content does not exist.',
            'question.required'   => 'Question is required.',
            'question.string'     => 'Question must be a valid string.',
            'question.max'        => 'Question cannot exceed 1000 characters.',
            'answer.required'     => 'Answer is required.',
            'answer.string'       => 'Answer must be a valid string.',
            'answer.max'          => 'Answer cannot exceed 1000 characters.',
        ];
    }
}
