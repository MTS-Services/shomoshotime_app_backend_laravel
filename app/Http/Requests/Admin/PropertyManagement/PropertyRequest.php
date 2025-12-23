<?php

namespace App\Http\Requests\Admin\PropertyManagement;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'property_type_id' => 'nullable|exists:property_types,id',
            'area_id' => 'required|exists:areas,id',
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date|after:today',
            'renew_at' => 'nullable|date|after:expires_at',
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
            "file" => "required|image|mimes:jpeg,png,jpg,webp,svg,mp4|max:2048",
            "files.*" => "nullable|image|mimes:jpeg,png,jpg,webp,svg,mp4|max:12288",
            "files" => "nullable|array|max:11",
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
            "file" => "nullable|image|mimes:jpeg,png,jpg,webp,svg,mp4|max:2048",
            "files.*" => "nullable|image|mimes:jpeg,png,jpg,webp,svg,mp4",
            "files" => "nullable|array|max:11",
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
            'category_id.required' => 'validation.property.category_id.required',
            'category_id.exists' => 'validation.property.category_id.exists',
            'property_type_id.exists' => 'validation.property.property_type_id.exists',
            'area_id.required' => 'validation.property.area_id.required',
            'area_id.exists' => 'validation.property.area_id.exists',
            'title.required' => 'validation.property.title.required',
            'title.string' => 'validation.property.title.string',
            'title.max' => 'validation.property.title.max',
            'title_ar.string' => 'validation.property.title_ar.string',
            'title_ar.max' => 'validation.property.title_ar.max',
            'description.string' => 'validation.property.description.string',
            'description_ar.string' => 'validation.property.description_ar.string',
            'price.required' => 'validation.property.price.required',
            'price.numeric' => 'validation.property.price.numeric',
            'price.min' => 'validation.property.price.min',
            'expires_at.date' => 'validation.property.expires_at.date',
            'expires_at.after' => 'validation.property.expires_at.after',
            'renew_at.date' => 'validation.property.renew_at.date',
            'renew_at.after' => 'validation.property.renew_at.after',
            'file.required' => 'validation.property.file.required',
            'file.image' => 'validation.property.file.image',
            'file.mimes' => 'validation.property.file.mimes',
            'file.max' => 'validation.property.file.max',
            'files.required' => 'validation.property.files.required',
            'files.array' => 'validation.property.files.array',
            'files.max' => 'validation.property.files.max',
            'files.*.image' => 'validation.property.files.star.image',
            'files.*.mimes' => 'validation.property.files.star.mimes',
            'files.*.max' => 'validation.property.files.star.max',
        ];
    }
}
