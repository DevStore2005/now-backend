<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Foundation\Http\FormRequest;

class ProviderPlanStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->role === UserType::PROVIDER;
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
            'type' => 'required|string|in:Weekly,BiWeekly,Monthly',
            'duration' => 'required|numeric',
            'off' => 'required|numeric|between:0,100',
            'description' => 'string|nullable|max:1000',
        ];
    }
}
