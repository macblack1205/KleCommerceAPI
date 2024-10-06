<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'review' => 'sometimes|required|string|max:1000',
            'product_id' => 'required|exists:products,id',
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
