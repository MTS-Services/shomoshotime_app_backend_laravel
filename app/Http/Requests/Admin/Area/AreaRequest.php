<?php

namespace App\Http\Requests\Admin\Area;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
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
            'slug' => ['required', 'string', Rule::unique('areas', 'slug')],
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
            'slug' => ['required', 'string', Rule::unique('areas', 'slug')->ignore(decrypt($this->route('area')))],
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
            'name.required' => 'validation.area.name.required',
            'name.string' => 'validation.area.name.string',
            'name.min' => 'validation.area.name.min',
            'name.max' => 'validation.area.name.max',
            'name_ar.required' => 'validation.area.name_ar.required',
            'name_ar.string' => 'validation.area.name_ar.string',
            'name_ar.min' => 'validation.area.name_ar.min',
            'name_ar.max' => 'validation.area.name_ar.max',
            'slug.required' => 'validation.area.slug.required',
            'slug.string' => 'validation.area.slug.string',
            'slug.unique' => 'validation.area.slug.unique',
        ];
    }
}
