<?php

namespace App\Http\Requests\Admin\PropertyManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyTypeRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:50',
            'name_ar' => 'required|string|min:3|max:50',
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
            'slug' => ['required', 'string', Rule::unique('property_types', 'slug')],
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
            'slug' => ['required', 'string', Rule::unique('property_types', 'slug')->ignore(decrypt($this->route('property_type')))],
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
            'name.required' => 'validation.property_type.name.required',
            'name.string' => 'validation.property_type.name.string',
            'name.min' => 'validation.property_type.name.min',
            'name.max' => 'validation.property_type.name.max',
            'name_ar.required' => 'validation.property_type.name_ar.required',
            'name_ar.string' => 'validation.property_type.name_ar.string',
            'name_ar.min' => 'validation.property_type.name_ar.min',
            'name_ar.max' => 'validation.property_type.name_ar.max',
            'slug.required' => 'validation.property_type.slug.required',
            'slug.string' => 'validation.property_type.slug.string',
            'slug.unique' => 'validation.property_type.slug.unique',
        ];
    }
}
