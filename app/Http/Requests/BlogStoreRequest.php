<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Foundation\Http\FormRequest;

class BlogStoreRequest extends FormRequest
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
            'title' => 'required|string',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,svg|max:4096',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|array',
            'content.*' => 'json',
            'image' => 'array|nullable',
            'image.*' => 'image|mimes:jpeg,png,jpg,svg|max:4096',
            'og_title' => 'nullable',
            'og_description' => 'nullable',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,svg',
        ];
    }
}
