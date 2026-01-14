<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;
use App\Models\UserSubscriptions;

class UserSubscriptionRequest extends BaseRequest
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
            'user_id'         => 'sometimes|required|exists:users,id',
            'subscription_id' => 'sometimes|required|exists:subscriptions,id',
            'starts_at'       => 'sometimes|required|date',
            'ends_at'         => 'sometimes|required|date|after:starts_at',
            'is_active'       => 'sometimes|boolean',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'user_id.required'         => 'User ID is required.',
            'user_id.exists'           => 'Selected user does not exist.',

            'subscription_id.required' => 'Subscription ID is required.',
            'subscription_id.exists'   => 'Selected subscription does not exist.',

            'starts_at.required'       => 'Start date is required.',
            'starts_at.date'           => 'Start date must be a valid date.',

            'ends_at.required'         => 'End date is required.',
            'ends_at.date'             => 'End date must be a valid date.',
            'ends_at.after'            => 'End date must be after start date.',

            'is_active.boolean'        => 'Is Active must be true or false.',
        ];
    }
}
