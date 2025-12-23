<?php

namespace App\Http\Requests\API\V1\PropertyManagement;

use App\Http\Requests\API\BaseRequest;
use App\Models\Property;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PropertyUpdateRequest extends BaseRequest
{

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
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'property_type_id' => ['sometimes', 'required', 'exists:property_types,id'],
            'area_id' => ['sometimes', 'required', 'exists:areas,id'],
            'title' => ['sometimes', 'nullable', 'string', 'min:3', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'min:3', 'max:255', Rule::unique('properties', 'slug')->ignore($this->route('slug'))],
            'description' => ['sometimes', 'required', 'string', 'min:3'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'required', 'integer', Rule::in([Property::STATUS_PENDING, Property::STATUS_OPEN, Property::STATUS_ARCHIVE])],
            'primary_file' => [
                'sometimes',
                'required',
                'file',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp,tiff,ico,mp4,mov,avi,wmv,flv,webm,mkv,3gp,ogv,ts,mpg,mpeg,vob'
            ],
            'files' => [
                'sometimes',
                'nullable',
                'array',
                'max:11'
            ],
            'files.*' => [
                'nullable',
                'file',
                'mimes:jpeg,jpg,png,gif,bmp,svg,webp,tiff,ico,mp4,mov,avi,wmv,flv,webm,mkv,3gp,ogv,ts,mpg,mpeg,vob'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => __('validation.property.category_id.required'),
            'category_id.exists' => __('validation.property.category_id.exists'),
            'property_type_id.required' => __('validation.property.property_type_id.required'),
            'property_type_id.exists' => __('validation.property.property_type_id.exists'),
            'area_id.required' => __('validation.property.area_id.required'),
            'area_id.exists' => __('validation.property.area_id.exists'),
            'title.nullable' => __('validation.property.title.nullable'),
            'title.string' => __('validation.property.title.string'),
            'title.min' => __('validation.property.title.min'),
            'title.max' => __('validation.property.title.max'),
            'slug.nullable' => __('validation.property.slug.nullable'),
            'slug.string' => __('validation.property.slug.string'),
            'slug.min' => __('validation.property.slug.min'),
            'slug.max' => __('validation.property.slug.max'),
            'slug.unique' => __('validation.property.slug.unique'),
            'description.required' => __('validation.property.description.required'),
            'description.string' => __('validation.property.description.string'),
            'description.min' => __('validation.property.description.min'),
            'price.required' => __('validation.property.price.required'),
            'price.numeric' => __('validation.property.price.numeric'),
            'price.min' => __('validation.property.price.min'),
            'status.required' => __('validation.property.status.required'),
            'status.integer' => __('validation.property.status.integer'),
            'status.in' => __('validation.property.status.in'),
            'primary_file.required' => __('validation.property.primary_file.required'),
            'primary_file.file' => __('validation.property.primary_file.file'),
            'primary_file.mimes' => __('validation.property.primary_file.mimes'),
            'files.array' => __('validation.property.files.array'),
            'files.max' => __('validation.property.files.max'),
            'files.*.file' => __('validation.property.files.star.file'),
            'files.*.mimes' => __('validation.property.files.star.mimes'),
        ];
    }

    // protected function prepareForValidation(): void
    // {
    //     if ($this->has('title') || $this->has('slug')) {
    //         $this->merge([
    //             'slug' => Str::slug($this->input('slug') ?? $this->input('title')),
    //         ]);
    //     }
    // }
}
