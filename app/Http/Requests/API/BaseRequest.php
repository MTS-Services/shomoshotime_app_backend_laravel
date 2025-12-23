<?php

namespace App\Http\Requests\API;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            // 'message' => 'Invalid data sent',
            // 'message' => implode(' ', $errors->all()),
            // 'token' => null,
            'data' => $errors->messages(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
        throw new HttpResponseException($response);
    }
}
