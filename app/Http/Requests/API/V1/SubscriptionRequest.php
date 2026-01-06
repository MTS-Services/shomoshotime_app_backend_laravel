<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;
use App\Models\Subscription;

class SubscriptionRequest extends BaseRequest
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
            'duration'   => 'sometimes|required|string|max:255',
            'price'      => 'sometimes|required|integer|min:0',
            'features'   => 'sometimes|required|array|min:1',
            'features.*' => 'string',
            'tag'        => 'nullable|string|max:255',
            'status'     => 'sometimes|required|integer|in:' . Subscription::STATUS_INACTIVE . ',' . Subscription::STATUS_ACTIVE,
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [

            'duration.required'   => 'Duration is required.',
            'duration.string'     => 'Duration must be a valid string.',
            'duration.max'        => 'Duration cannot exceed 255 characters.',

            'price.required'      => 'Price is required.',
            'price.integer'       => 'Price must be a valid number.',
            'price.min'           => 'Price must be at least 0.',

            'features.required'   => 'At least one feature is required.',
            'features.array'      => 'Features must be an array.',
            'features.*.string'   => 'Each feature must be a valid string.',

            'tag.string'          => 'Tag must be a valid string.',
            'tag.max'             => 'Tag cannot exceed 255 characters.',

            'status.required'     => 'Status is required.',
            'status.integer'      => 'Status must be a valid number.',
            'status.in'           => 'Status must be either 0 (inactive) or 1 (active).',
        ];
    }
}
