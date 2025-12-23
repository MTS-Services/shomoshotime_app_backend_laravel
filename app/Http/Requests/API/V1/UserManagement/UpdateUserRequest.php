<?php

namespace App\Http\Requests\API\V1\UserManagement;

use App\Http\Requests\API\BaseRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Rules\KuwaitPhoneNumber;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends BaseRequest
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
        $userId = request()->user()->id;
        return [
            // User Table Informations
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                new KuwaitPhoneNumber(),
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'image' => ['sometimes', 'required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'user_type' => ['sometimes', 'required', 'integer', Rule::in([User::USER_TYPE_INDIVIDUAL, User::USER_TYPE_AGENT])],
            'language_preference' => ['sometimes', 'required', 'integer', Rule::in(array_keys(User::languageList()))],
            'status' => ['sometimes', 'required', 'integer', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])],

            // User Profile Table Information
            'dob' => ['sometimes', 'required', 'date_format:d/m/Y', 'before_or_equal:' . now()->subYears(13)->format('d/m/Y')],
            'gender' => ['sometimes', 'required', 'integer', Rule::in(array_keys(UserProfile::genderList()))],
            'city' => ['sometimes', 'required', 'string', 'max:255'],
            'country' => ['sometimes', 'required', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'required', 'string', 'max:20'],
            'bio' => ['sometimes', 'required', 'string', 'max:1000'],
            'website' => ['sometimes', 'required', 'url', 'max:255'],
            'social_links' => ['sometimes', 'required', 'array'],
            'social_links.*' => ['sometimes', 'required', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            // User Table Informations
            'name.required' => __('validation.user.name.required'),
            'name.string' => __('validation.user.name.string'),
            'name.max' => __('validation.user.name.max'),

            'email.required' => __('validation.user.email.required'),
            'email.email' => __('validation.user.email.email'),
            'email.max' => __('validation.user.email.max'),
            'email.unique' => __('validation.user.email.unique'),

            'phone.required' => __('validation.user.phone.required'),
            'phone.string' => __('validation.user.phone.string'),
            'phone.unique' => __('validation.user.phone.unique'),

            'image.required' => __('validation.user.image.required'),
            'image.image' => __('validation.user.image.image'),
            'image.mimes' => __('validation.user.image.mimes'),
            'image.max' => __('validation.user.image.max'),

            'user_type.required' => __('validation.user.user_type.required'),
            'user_type.integer' => __('validation.user.user_type.integer'),
            'user_type.in' => __('validation.user.user_type.in'),

            'language_preference.required' => __('validation.user.language_preference.required'),
            'language_preference.integer' => __('validation.user.language_preference.integer'),
            'language_preference.in' => __('validation.user.language_preference.in'),

            'status.required' => __('validation.user.status.required'),
            'status.integer' => __('validation.user.status.integer'),
            'status.in' => __('validation.user.status.in'),

            // User Profile Table Information
            'dob.required' => __('validation.user.dob.required'),
            'dob.date_format' => __('validation.user.dob.date_format'),
            'dob.before_or_equal' => __('validation.user.dob.before_or_equal'),

            'gender.required' => __('validation.user.gender.required'),
            'gender.integer' => __('validation.user.gender.integer'),
            'gender.in' => __('validation.user.gender.in'),

            'city.required' => __('validation.user.city.required'),
            'city.string' => __('validation.user.city.string'),
            'city.max' => __('validation.user.city.max'),

            'country.required' => __('validation.user.country.required'),
            'country.string' => __('validation.user.country.string'),
            'country.max' => __('validation.user.country.max'),

            'postal_code.required' => __('validation.user.postal_code.required'),
            'postal_code.string' => __('validation.user.postal_code.string'),
            'postal_code.max' => __('validation.user.postal_code.max'),

            'bio.required' => __('validation.user.bio.required'),
            'bio.string' => __('validation.user.bio.string'),
            'bio.max' => __('validation.user.bio.max'),

            'website.required' => __('validation.user.website.required'),
            'website.url' => __('validation.user.website.url'),
            'website.max' => __('validation.user.website.max'),

            'social_links.required' => __('validation.user.social_links.required'),
            'social_links.array' => __('validation.user.social_links.array'),
            'social_links.*.required' => __('validation.user.social_links.star.required'),
            'social_links.*.url' => __('validation.user.social_links.star.url'),
            'social_links.*.max' => __('validation.user.social_links.star.max'),
        ];
    }
}
