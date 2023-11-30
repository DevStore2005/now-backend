<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->role == UserType::ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:payment_methods,name',
            'icon' => 'required|mimes:jpeg,png,jpg,svg|max:2048'
        ];
    }
}
