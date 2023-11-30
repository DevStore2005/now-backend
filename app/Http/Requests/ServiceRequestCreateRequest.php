<?php

namespace App\Http\Requests;

use App\Utils\UserType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequestCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->role == UserType::USER;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $requiredIf = fn ($key) => Rule::requiredIf(!$key);
        if ($this->is_hourly) {
            return [
                'is_hourly' => "in:1," . true,
                'provider_id' => 'required|exists:users,id',
                'address' => 'required|min:10',
                'questions' => 'required',
                'token' => [
                    $requiredIf($this->card_id),
                    'starts_with:tok_',
                    'nullable'
                ],
                'card_id' => [
                    $requiredIf($this->token),
                    'starts_with:card_',
                    'nullable'
                ],
                'hours' => 'min:1|max:2',
                'date' => 'required|date|after_or_equal:today',
                'book_time_slots' => 'required|array|min:1',
                'book_time_slots.*.start' => 'required|date_format:H:i',
                'book_time_slots.*.end' => 'required|date_format:H:i',
                'plan_id' => [
                    Rule::exists('plans', 'id')->where(function ($query) {
                        $query->whereProvider_id($this->provider_id);
                    }),
                    'nullable'
                ],
            ];
        } else {
            return [
                'first_name' => 'string|max:255',
                'last_name' => 'string|max:255',
                'email' => 'email:rfc,dns|max:255',
                'phone' => 'string|max:100',
                'is_hourly' => 'in:0,' . false,
                'provider_id' => 'required',
                'address' => 'required|min:10',
                'questions' => 'required',
                'detail' => 'min:20|max:200',
                'images.*' => 'image|mimes:jpeg,png,jpg,svg|max:4096',
                'files.*' => 'file|mimes:pdf,doc,docx|max:4096',
            ];
        }
    }
}
