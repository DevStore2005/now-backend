<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLinkRequest extends FormRequest
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
            'page' => 'string|nullable',
            'type' => 'string|nullable',
            'name' => 'nullable|string|max:255',
            'url' => 'required|string',
            'description' => 'string|max:1000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(){
        return [
            'page.string' => 'The page field must be a string.',
            'type.string' => 'The type field must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'url.required' => 'The url field is required.',
            'description.required'  => 'The description field is required.',
            'description.max' => 'The description may not be greater than 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * 
     * @return array
     */
    public function attributes(){
        return [
            'page' => 'Page',
            'name' => 'Name',
            'url' => 'Url',
            'description' => 'Description',
        ];
    }
}
