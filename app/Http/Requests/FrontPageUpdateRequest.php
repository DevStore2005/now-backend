<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Foundation\Http\FormRequest;

class FrontPageUpdateRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|min:3|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ];
    }
}
