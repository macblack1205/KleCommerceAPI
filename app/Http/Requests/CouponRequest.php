<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'coupon' => 'sometimes|required|string|max:255|unique:coupons,coupon',
            'discount_percentage' => 'sometimes|required|numeric|min:0|max:100',
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
