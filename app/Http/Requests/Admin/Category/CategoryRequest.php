<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
            'description' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:255',
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
            'slug' => ['required', 'string', Rule::unique('categories', 'slug')],
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
            'slug' => ['required', 'string', Rule::unique('categories', 'slug')->ignore(decrypt($this->route('category')))],
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
            'name.required' => 'validation.category.name.required',
            'name.string' => 'validation.category.name.string',
            'name.min' => 'validation.category.name.min',
            'name.max' => 'validation.category.name.max',
            'name_ar.required' => 'validation.category.name_ar.required',
            'name_ar.string' => 'validation.category.name_ar.string',
            'name_ar.min' => 'validation.category.name_ar.min',
            'name_ar.max' => 'validation.category.name_ar.max',
            'description.string' => 'validation.category.description.string',
            'description.max' => 'validation.category.description.max',
            'description_ar.string' => 'validation.category.description_ar.string',
            'description_ar.max' => 'validation.category.description_ar.max',
            'slug.required' => 'validation.category.slug.required',
            'slug.string' => 'validation.category.slug.string',
            'slug.unique' => 'validation.category.slug.unique',
        ];
    }
}
