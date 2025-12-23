<?php

namespace App\Http\Requests\API\V1\UserManagement;

use App\Http\Requests\API\BaseRequest;

class CompanyInformationRequest extends BaseRequest
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
            'company_name' => ['required', 'string', 'min:3', 'max:255'],
            'company_description' => ['nullable', 'string', 'min:3', 'max:255'],
            'address' => ['nullable', 'string', 'min:3', 'max:255'],
            'website' => ['nullable', 'url'],
            'whatsapp_number' => ['nullable', 'string', 'min:3', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp,tiff', 'min:1', 'max:2048'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => __('validation.company.company_name.required'),
            'company_name.string' => __('validation.company.company_name.string'),
            'company_name.min' => __('validation.company.company_name.min'),
            'company_name.max' => __('validation.company.company_name.max'),
            'company_description.string' => __('validation.company.company_description.string'),
            'company_description.min' => __('validation.company.company_description.min'),
            'company_description.max' => __('validation.company.company_description.max'),
            'address.string' => __('validation.company.address.string'),
            'address.min' => __('validation.company.address.min'),
            'address.max' => __('validation.company.address.max'),
            'website.url' => __('validation.company.website.url'),
            'whatsapp_number.string' => __('validation.company.whatsapp_number.string'),
            'whatsapp_number.min' => __('validation.company.whatsapp_number.min'),
            'whatsapp_number.max' => __('validation.company.whatsapp_number.max'),
            'image.image' => __('validation.company.image.image'),
            'image.mimes' => __('validation.company.image.mimes'),
            'image.min' => __('validation.company.image.min'),
            'image.max' => __('validation.company.image.max'),
            'social_links.array' => __('validation.company.social_links.array'),
            'social_links.*.url' => __('validation.company.social_links.star.url'),
        ];
    }
}
