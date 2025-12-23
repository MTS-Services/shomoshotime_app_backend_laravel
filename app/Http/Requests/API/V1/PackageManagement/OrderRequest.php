<?php

namespace App\Http\Requests\API\V1\PackageManagement;

use App\Http\Requests\API\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends BaseRequest
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
            'subscription_id' => 'required|exists:subscription_plans,id',
            'order_id'        => 'nullable|string|max:100|unique:orders,order_id',
            'notes'           => 'nullable|string|max:500',
            'amount'          => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'The subscription is required.',
            'subscription_id.exists'   => 'The selected subscription does not exist.',
            'order_id.unique'          => 'The order ID must be unique.',
            'amount.required'         => 'The amount is required.',
            'amount.numeric'          => 'The amount must be a number.',
            'amount.min'              => 'The amount must be at least 0.',
        ];
    }
}
