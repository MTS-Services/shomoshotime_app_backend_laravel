<?php

namespace App\Http\Requests\Admin\PackageManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionPlanRequest extends FormRequest
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
            'subscription_type_id' => 'required|exists:subscription_types,id',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'total_ads' => 'nullable|integer|min:1',
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
            'name.required' => 'validation.subscription_plan.name.required',
            'name.string' => 'validation.subscription_plan.name.string',
            'name.min' => 'validation.subscription_plan.name.min',
            'name.max' => 'validation.subscription_plan.name.max',
            'subscription_type_id.required' => 'validation.subscription_plan.subscription_type_id.required',
            'subscription_type_id.exists' => 'validation.subscription_plan.subscription_type_id.exists',
            'price.required' => 'validation.subscription_plan.price.required',
            'price.numeric' => 'validation.subscription_plan.price.numeric',
            'price.min' => 'validation.subscription_plan.price.min',
            'duration_days.required' => 'validation.subscription_plan.duration_days.required',
            'duration_days.integer' => 'validation.subscription_plan.duration_days.integer',
            'duration_days.min' => 'validation.subscription_plan.duration_days.min',
            'total_ads.integer' => 'validation.subscription_plan.total_ads.integer',
            'total_ads.min' => 'validation.subscription_plan.total_ads.min',
        ];
    }
}
