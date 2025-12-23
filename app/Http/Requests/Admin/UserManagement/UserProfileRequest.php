<?php

namespace App\Http\Requests\Admin\UserManagement;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
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
            'user_id'       => 'required|exists:users,id',
            'sort_order'    => 'nullable|integer',
            'dob'           => 'nullable|date',
            'gender'        => 'nullable|in:0,1,2',
            'city'       => 'nullable|string|max:255',
            'city_ar'       => 'nullable|string|max:255',
            'country'    => 'nullable|string|max:255',
            'country_ar'    => 'nullable|string|max:255',
            'postal_code'   => 'nullable|string|max:20',
            'bio_en'        => 'nullable|string',
            'bio_ar'        => 'nullable|string',
            'website'       => 'nullable|url',
            'social_links'  => 'nullable|array',
            'medianames'    => 'nullable|array',
            'medialinks'    => 'nullable|array',
        ];
    }
}
