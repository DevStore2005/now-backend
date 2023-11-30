<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockedSlotStoreRequest extends FormRequest
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
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today'
            ],
            'from_time' => [
                'date_format:H:i',
                'nullable'
            ],
            'to_time' => [
                'date_format:H:i',
                'nullable'
            ],
        ];
    }
}
