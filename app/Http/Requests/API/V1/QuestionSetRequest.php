<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;
use App\Models\QuestionSet;

class QuestionSetRequest extends BaseRequest
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
            'category'   => 'sometimes|required|string|max:100',
            'title'      => 'sometimes|required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'status'     => 'sometimes|required|integer|in:' . implode(',', array_keys(QuestionSet::getStatusList())),
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [

            'category.required'   => 'Category is required.',
            'category.string'     => 'Category must be text.',
            'category.max'        => 'Category may not be greater than 100 characters.',

            'title.required'      => 'Title is required.',
            'title.string'        => 'Title must be text.',
            'title.max'           => 'Title may not be greater than 255 characters.',

            'subtitle.string'     => 'Subtitle must be text.',
            'subtitle.max'        => 'Subtitle may not be greater than 255 characters.',

            'status.required'     => 'Status is required.',
            'status.integer'      => 'Status must be a valid number.',
            'status.in'           => 'Invalid status. Allowed values: ' . implode(', ', array_keys(QuestionSet::getStatusList())),
        ];
    }
}
