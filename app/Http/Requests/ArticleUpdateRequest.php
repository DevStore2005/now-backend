<?php

namespace App\Http\Requests;

use App\Utils\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class ArticleUpdateRequest extends FormRequest
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
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'sub_service_id' => 'nullable|exists:sub_services,id',
            'content' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The :attribute field is required.',
            'title.max' => 'The :attribute may not greater than :max characters.',
            'sub_service_id.exists' => 'The :attribute field is invalid.',
            'content.required' => 'The :attribute field is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'title',
            'sub_service_id' => 'sub service',
            'content' => 'content',
        ];
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param  array<string>  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $errors): JsonResponse
    {
        $thisErrors = [];
        foreach ($errors as $key => $error) {
            $thisErrors[$key] = $error[0];
        }
        return response()->json([
            'status' => 'error',
            'message' => $thisErrors,
        ], HttpStatusCode::UNPROCESSABLE_ENTITY);
    }
}
