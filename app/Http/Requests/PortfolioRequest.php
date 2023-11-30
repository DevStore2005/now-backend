<?php

namespace App\Http\Requests;

use App\Models\Portfolio;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PortfolioRequest extends FormRequest
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
            'length' => [
                'required', 'numeric', 'between:1,8',
                function ($attribute, $value, $fail) {
                    $Portfolios = Portfolio::where('provider_id', auth()->user()->id)->count();
                    if ($Portfolios + $value > 8) {
                        $fail('You already address 8 portfolios');
                    }
                    if ($Portfolios + $value < 3) {
                        $fail('You must have at least 3 portfolios');
                    }
                    // for ($index = 0; $index < $value; $index++) {
                    //     if (isset($request['images_' . $index]) == false || isset($request['description_' . $index]) == false) {
                    //         $fail('You must fill all fields');
                    //     }
                    // }
                },
            ],
            // 'images' => 'required|array|min:3|max:8',
            // 'images.*' => 'required|image|mimes:jpeg,png,jpg,svg|max:4096',
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
            'length.required' => 'Length is required',
            'length.numeric' => 'Length must be numeric',
            'length.between' => 'Length must be between 1 and 8',
            // 'description.required' => 'Please provide a description',
            // 'description.string' => 'Description must be a string',
            // 'images.required' => 'Please provide at least 3 images',
            // 'images.array' => 'Images must be an array',
            // 'images.min' => 'Please provide at least 3 images',
            // 'images.max' => 'Please provide at most 8 images',
            // 'images.*.required' => 'Please provide an image',
            // 'images.*.image' => 'Image must be an image',
            // 'images.*.mimes' => 'Image must be a jpeg, png, jpg, or svg',
            // 'images.*.max' => 'Image must be less than 4096kb',
        ];
    }
}
