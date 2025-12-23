<?php

namespace App\Http\Requests\Admin\UserManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048', // Added max size rule
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')], // Added max size rule
            'phone' => ['required', 'string', 'max:255', Rule::unique('users', 'phone')], // Added max size rule
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore(decrypt($this->route('admin')))], // Added max size rule
            'phone' => ['required', 'string', 'max:255', Rule::unique('users', 'phone')->ignore(decrypt($this->route('admin')))], // Added max size rule
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Made password nullable on update
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
            'name.required' => 'validation.admin.name.required',
            'name.string' => 'validation.admin.name.string',
            'name.min' => 'validation.admin.name.min',
            'name.max' => 'validation.admin.name.max',
            'last_name.string' => 'validation.admin.last_name.string',
            'last_name.min' => 'validation.admin.last_name.min',
            'last_name.max' => 'validation.admin.last_name.max',
            'first_name_ar.required' => 'validation.admin.first_name_ar.required',
            'first_name_ar.string' => 'validation.admin.first_name_ar.string',
            'first_name_ar.min' => 'validation.admin.first_name_ar.min',
            'first_name_ar.max' => 'validation.admin.first_name_ar.max',
            'last_name_ar.string' => 'validation.admin.last_name_ar.string',
            'last_name_ar.min' => 'validation.admin.last_name_ar.min',
            'last_name_ar.max' => 'validation.admin.last_name_ar.max',
            'image.image' => 'validation.admin.image.image',
            'image.mimes' => 'validation.admin.image.mimes',
            'image.max' => 'validation.admin.image.max',
            'email.required' => 'validation.admin.email.required',
            'email.string' => 'validation.admin.email.string',
            'email.email' => 'validation.admin.email.email',
            'email.max' => 'validation.admin.email.max',
            'email.unique' => 'validation.admin.email.unique',
            'phone.required' => 'validation.admin.phone.required',
            'phone.string' => 'validation.admin.phone.string',
            'phone.max' => 'validation.admin.phone.max',
            'phone.unique' => 'validation.admin.phone.unique',
            'password.required' => 'validation.admin.password.required',
            'password.string' => 'validation.admin.password.string',
            'password.min' => 'validation.admin.password.min',
            'password.confirmed' => 'validation.admin.password.confirmed',
        ];
    }
}
