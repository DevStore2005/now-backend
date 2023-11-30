<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleStoreRequest extends FormRequest
{
    /**
     * Week days
     *
     * @var string $weekDays
     */
    private $weekDays = "Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday";

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
            "is_custom" => "required",
            "from_time" => "required_if:is_custom,0|exclude_if:is_custom,1",
            "to_time" => "required_if:is_custom,0|exclude_if:is_custom,1",
            "days" => [
                "required",
                "array",
                "min:1",
                "max:7"
            ],
            "days.*.day" => [
                "in:{$this->weekDays}",
                "required",
                "distinct"
            ],
            "days.*.from_time" => [
                "required_if:is_custom,1",
                "date_format:H:i",
                "exclude_if:is_custom,0"
            ],
            "days.*.to_time" => [
                "required_if:is_custom,1",
                "date_format:H:i",
                "exclude_if:is_custom,0"
            ],
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
            "from.required" => "The :attribute field is required.",
            "from.in" => "The :attribute must be one of the following types: {$this->weekDays}",
            "from_time.date_format" => "The :attribute must be in the format of 00:00",
            "from_time.required" => "The :attribute field is required.",
            "to.required" => "The :attribute field is required.",
            "to.in" => "The :attribute must be one of the following types: {$this->weekDays}",
            "to_time.date_format" => "The :attribute must be in the format of 00:00",
            "to_time.required" => "The :attribute field is required.",
            "days.required" => "Please select at least one day.",
            "days.min" => "The :attribute must have at least :min items.",
            "days.max" => "The :attribute may not have more than :max items.",
            "days.*.day.in" => "The :attribute must be one of the following types: {$this->weekDays}",
            "days.*.from_time.date_format" => "The :attribute does not match the format 00:00.",
            "days.*.from_time.required_if" => "The :attribute field is required when is_custom is true.",
            "days.*.to_time.date_format" => "The :attribute does not match the format 00:00.",
            "days.*.to_time.required_if" => "The :attribute field is required when is_custom is true.",
        ];
    }
}
