<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sub_service_id' => 'exists:sub_services,id|nullable',
            'question' => 'required|string|min:3|max:255',
            'answers.*' => 'required|min:1|max:1200',
        ];
    }
}
