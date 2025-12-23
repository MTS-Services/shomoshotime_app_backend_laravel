<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\KuwaitPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', new KuwaitPhoneNumber()],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.profile_update.name.required'),
            'name.string' => __('validation.profile_update.name.string'),
            'name.max' => __('validation.profile_update.name.max'),
            'email.required' => __('validation.profile_update.email.required'),
            'email.string' => __('validation.profile_update.email.string'),
            'email.lowercase' => __('validation.profile_update.email.lowercase'),
            'email.email' => __('validation.profile_update.email.email'),
            'email.max' => __('validation.profile_update.email.max'),
            'email.unique' => __('validation.profile_update.email.unique'),
            'phone.string' => __('validation.phone.string'),
        ];
    }
}
