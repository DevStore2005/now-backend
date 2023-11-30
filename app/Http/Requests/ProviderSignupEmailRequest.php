<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProviderSignupEmailRequest extends FormRequest
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
            'email' => [
                'required',
                'email:rfc,dns',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereRole(UserType::PROVIDER)
                        ->whereNotNull('email_verified_at')
                        ->whereNull('deleted_at');
                })
            ]
        ];
    }
}
