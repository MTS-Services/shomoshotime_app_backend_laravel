<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Http\Requests\API\BaseRequest;
use App\Rules\KuwaitPhoneNumber;

class LoginRequest extends BaseRequest
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
            'fcm_token' => ['nullable', 'string'],
            'phone' => ['required', new KuwaitPhoneNumber()],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            // Phone field messages
            'phone.required' => __('validation.phone.required'),

            // Password field messages
            'password.required' => __('validation.password.required'),
            'password.string' => __('validation.password.string'),
            'password.min' => __('validation.password.min'),
        ];
    }
}
