<?php

namespace App\Http\Requests\Admin\PackageManagement;

use App\Models\Package;
use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tag' => 'required|in:' . implode(',', array_keys(Package::tagList())),
            'total_ad' => 'required|numeric',
            'price' => 'required|numeric',
        ] + ($this->isMethod('POST') ? $this->store() : $this->update());
    }

    /**
     * Get the validation rules for a new record.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function store(): array
    {
        return [
        ];
    }

    /**
     * Get the validation rules for an existing record.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function update(): array
    {
        return [
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tag.required' => 'validation.package.tag.required',
            'tag.in' => 'validation.package.tag.in',
            'total_ad.required' => 'validation.package.total_ad.required',
            'total_ad.numeric' => 'validation.package.total_ad.numeric',
            'price.required' => 'validation.package.price.required',
            'price.numeric' => 'validation.package.price.numeric',
        ];
    }
}
