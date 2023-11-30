<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
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
            'blog_id' => ["required_if:comment_id," . false, 'exists:blogs,id'],
            'comment' => 'required|string',
            'comment_id' => ["required_if:blog_id," . false, 'exists:comments,id'],
        ];
    }

    public function messages()
    {
        return [
            'blog_id.required_if' => 'Blog id is required when comment id is not provided',
            'blog_id.exists' => 'Blog id does not exist',
            'comment.required' => 'Comment is required',
            'comment.string' => 'Comment must be a string',
            'comment_id.required_if' => 'Comment id is required when blog id is not provided',
            'comment_id.exists' => 'Comment id does not exist',
        ];
    }
}
