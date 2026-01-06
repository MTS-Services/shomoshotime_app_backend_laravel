<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;

class UserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes','required', 'email', $userId ? 'unique:users,email,'.$userId : 'unique:users,email'],
            'password' => [$userId ? 'nullable' : 'required','min:6',],
            'status' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'image.image' => 'Image must be a valid image file.',
            'image.mimes' => 'Image must be a jpg, jpeg, or png file.',
            'image.max' => 'Image must not exceed 2MB in size.',
        ];
    }
}
