<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'street' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|required|string|max:255',
        ];
    }
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Please check the input data.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
