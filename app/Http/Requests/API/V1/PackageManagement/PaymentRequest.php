<?php

namespace App\Http\Requests\API\V1\PackageManagement;

use App\Http\Requests\API\BaseRequest;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends BaseRequest
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
        $order = Order::where('order_id', $this->order_id)->first();
        if (!$order) {
            return [
                'order_id' => 'required|exists:orders,order_id',
            ];
        }
        return [
            'order_id' => 'required|exists:orders,order_id',
            'amount' => 'required|numeric|min:' . $order->amount,
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ];
    }

    // public function messages(): array
    // {
    //     return [
    //         'user_id.required' => 'The user is required.',
    //         'user_id.exists' => 'The selected user does not exist.',
    //         'order_id.required' => 'The order is required.',
    //         'order_id.exists' => 'The selected order does not exist.',
    //         'payment_method.required' => 'The payment method is required.',
    //         'payment_provider_id.exists' => 'The selected payment provider does not exist.',
    //         'amount.required' => 'The amount is required.',
    //         'amount.numeric' => 'The amount must be a number.',
    //         'amount.min' => 'The amount must be at least 0.',
    //         'currency.required' => 'The currency is required.',
    //         'currency.size' => 'The currency must be a 3-letter code.',
    //         'credits_purchased.integer' => 'The credits purchased must be an integer.',
    //         'credits_purchased.min' => 'The credits purchased must be at least 0.',
    //         'exchange_rate.numeric' => 'The exchange rate must be a number.',
    //         'exchange_rate.min' => 'The exchange rate must be at least 0.',
    //         'payment_intent_id.string' => 'The payment intent ID must be a string.',
    //         'payment_intent_id.max' => 'The payment intent ID may not be greater than 255 characters.',
    //         'receipt_url.url' => 'The receipt URL must be a valid URL.',
    //         'receipt_url.max' => 'The receipt URL may not be greater than 255 characters.',
    //         'failure_reason.string' => 'The failure reason must be a string.',
    //         'failure_reason.max' => 'The failure reason may not be greater than 500 characters.',
    //         'metadata.array' => 'The metadata must be an array.',
    //         'processed_at.date' => 'The processed at date must be a valid date.',

    //     ];
    // }
}
