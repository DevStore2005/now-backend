<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class BuyCreditRequest extends FormRequest
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
        $requiredIf = fn ($key) => Rule::requiredIf(!$key);
        return [
            'stripe_name' => ['required', 'string', 'exists:plans,stripe_name'],
            'token' => [$requiredIf($this->card_id), 'starts_with:tok_', 'nullable'],
            'card_id' => [$requiredIf($this->token), 'starts_with:card_', 'nullable'],
        ];
    }
}
