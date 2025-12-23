<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Http\Requests\API\BaseRequest;
use App\Rules\KuwaitPhoneNumber;

class RegistrationRequest extends BaseRequest
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
            'fcm_token' => ['required', 'string'],
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:50'],
            'phone' => ['required', 'unique:users', new KuwaitPhoneNumber()],
            'email' => ['nullable', 'email', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('validation.name.string'),
            'name.min' => __('validation.name.min'),
            'name.max' => __('validation.name.max'),

            'phone.required' => __('validation.phone.required'),
            'phone.unique' => __('validation.phone.unique'),

            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.unique' => __('validation.email.unique'),

            'password.required' => __('validation.password.required'),
            'password.string' => __('validation.password.string'),
            'password.min' => __('validation.password.min'),
            'password.confirmed' => __('validation.password.confirmed'),
        ];
    }
}
