<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
    public function rules(): array
    {
        $userId = $this->route('id');
        $rules = [
            'name' => 'required|string|between:2,255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6',
            'address' => 'required|string|',
            'phone' => 'required|string|min:10|max:11|unique:users|regex:/^[0-9]{10,11}$/',
            'image' => 'file|mimes:png,jpg,jpeg'
        ];
        if ($userId && is_numeric($userId)) {
            $rules['password'] = '';
            $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $userId;
            $rules['phone'] = 'required|string|min:10|max:11|regex:/^[0-9]{10,11}$/|unique:users,phone,' . $userId;
        }
        return $rules;
    }
    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => ':attribute is required',
            'email' => ':attribute is not the correct format',
            'min' => ':attribute cannot be less than :min',
            'max' => ':attribute cannot be greater than :max',
            'mimes' => ':attribute is not in the correct format(png,jpg,jpeg)',
            'unique' => ':attribute already exists',
            'file' => ':attribute must be a file',
            'regex' => ':attribute is not in the correct format'
        ];
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'address' => 'Address',
            'phone' => 'Phone',
            'image' => 'Image'
        ];
    }
}
