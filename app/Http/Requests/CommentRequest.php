<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        $rulesUserComment = [
            'content' => 'string|max:255',
            'rate' => 'required|integer|min:1|max:5',
        ];

        $rulesUserReply = [
            'content' => 'required|string|max:255',
        ];

        $route = $request->route();

        if($route->uri() == "api/products/{id}/comments") {
            return $rulesUserComment;
        } else {
            return $rulesUserReply;
        }

    }

    /**
     * Get custom error messages for validator errors.
     * 
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'required' => ':attribute is required',
            'exists' => ':attribute is invalid',
        ];
    }

    /**
     * Get custom attribute names for validator attributes.
     * 
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'product_id' => 'Product',
            'reply_to_id' => 'Comment',
            'rate' => 'Rate',
            'content' => 'Content'
        ];
    }
}
