<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'surname' => 'sometimes|required|string|max:255',
            'age' => 'sometimes|required|integer|min:18|max:100',
            'country' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 
            Rule::unique('users', 'email')->ignore($this->route('id'))],
            'number' => 'sometimes|nullable|string|max:255',
            'password' => 'sometimes|required|string|min:4|confirmed',
            'photo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
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
