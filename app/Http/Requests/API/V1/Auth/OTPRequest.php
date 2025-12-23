<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Http\Requests\API\BaseRequest;

class OTPRequest extends BaseRequest
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
            'otp' => 'required|numeric|digits:4'
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => __('validation.otp.required'),
            'otp.numeric' => __('validation.otp.numeric'),
            'otp.digits' => __('validation.otp.digits'),
        ];
    }
}
