<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatatableRequest extends FormRequest
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
            'datas.*.id' => 'required|integer',
            'datas.*.newOrder' => 'required|integer',
            'model' => 'required|string',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'datas.*.id.required' => __('validation.datatable.datas_id.required'),
            'datas.*.id.integer' => __('validation.datatable.datas_id.integer'),
            'datas.*.newOrder.required' => __('validation.datatable.datas_new_order.required'),
            'datas.*.newOrder.integer' => __('validation.datatable.datas_new_order.integer'),
            'model.required' => __('validation.datatable.model.required'),
            'model.string' => __('validation.datatable.model.string'),
        ];
    }
}
