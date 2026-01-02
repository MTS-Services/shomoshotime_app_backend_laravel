<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;
use App\Models\Question;

class QuestionRequest extends BaseRequest
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
            'question_set_id'  => 'sometimes|required|exists:question_sets,id',
            'file'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', 
            'question'         => 'sometimes|required|string|max:1000',
            'option_a'         => 'sometimes|required|string|max:255',
            'option_b'         => 'sometimes|required|string|max:255',
            'option_c'         => 'sometimes|required|string|max:255',
            'option_d'         => 'sometimes|required|string|max:255',
            'answer'           => 'sometimes|required|string|in:option_a,option_b,option_c,option_d',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [

            'question_set_id.required' => 'Question set is required.',
            'question_set_id.exists'   => 'Selected question set does not exist.',

            'file.file'                => 'File must be a valid file.',
            'file.mimes'               => 'File must be a type of: jpg, jpeg, png, pdf.',
            'file.max'                 => 'File may not be greater than 10 MB.',

            'question.required'        => 'Question is required.',
            'question.string'          => 'Question must be text.',
            'question.max'             => 'Question may not be greater than 1000 characters.',

            'option_a.required'        => 'Option A is required.',
            'option_a.string'          => 'Option A must be text.',
            'option_a.max'             => 'Option A may not be greater than 255 characters.',

            'option_b.required'        => 'Option B is required.',
            'option_b.string'          => 'Option B must be text.',
            'option_b.max'             => 'Option B may not be greater than 255 characters.',

            'option_c.required'        => 'Option C is required.',
            'option_c.string'          => 'Option C must be text.',
            'option_c.max'             => 'Option C may not be greater than 255 characters.',

            'option_d.required'        => 'Option D is required.',
            'option_d.string'          => 'Option D must be text.',
            'option_d.max'             => 'Option D may not be greater than 255 characters.',

            'answer.required'          => 'Answer is required.',
            'answer.string'            => 'Answer must be text.',
            'answer.in'                => 'Answer must be one of: a, b, c, or d.',
        ];
    }
}
