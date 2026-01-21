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
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|integer|in:0,1',
            'is_publish' => 'sometimes|required|boolean',   
            'file' =>'sometimes|required|file|mimes:mp3,pdf',      
        ];
    }

 public function messages(): array
{
    return [
        // Title
        'title.required' => 'The title field is required.',
        'title.string'   => 'The title must be a valid string.',
        'title.max'      => 'The title may not be greater than 255 characters.',

        // Subtitle
        'subtitle.string' => 'The subtitle must be a valid string.',
        'subtitle.max'    => 'The subtitle may not be greater than 255 characters.',

        // Category
        'category.required' => 'The category field is required.',
        'category.string'   => 'The category must be a valid string.',
        'category.max'      => 'The category may not be greater than 255 characters.',

        // Type
        'type.required' => 'The type field is required.',
        'type.integer'  => 'The type must be an integer value.',
        'type.in'       => 'The selected type is invalid.',

        // Publish Status
        'is_publish.required' => 'The publish status field is required.',
        'is_publish.boolean'  => 'The publish status must be true or false.',

    ];
}

}
