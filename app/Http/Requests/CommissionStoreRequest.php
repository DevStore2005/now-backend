<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Foundation\Http\FormRequest;

class CommissionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->role == UserType::ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'percentage' => 'required|numeric|between:0,100',
            'status' => 'required|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'percentage.required' => 'The commission percentage is required!',
            'percentage.numeric' => 'The commission percentage must be a number!',
            'percentage.between' => 'The commission percentage must be between 0 and 100!',
            'status.required' => 'The status is required!',
            'status.boolean' => 'The status must be a boolean!',
        ];
    }
}
