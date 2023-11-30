<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Hamcrest\Description;
use Illuminate\Foundation\Http\FormRequest;

class PlanStoreRequest extends FormRequest
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
            'title' => 'required|string|max:255|unique:plans,title',
            'stripe_name' => 'required|string|max:255|unique:plans,stripe_name',
            'price' => 'required|numeric',
            'credit' => 'nullable|numeric',
            'threshold' => 'nullable|numeric|max:20',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
