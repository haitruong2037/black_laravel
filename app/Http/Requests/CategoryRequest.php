<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $categoriesId = $this->route('id');
        $rules = [
            'name' => 'required',
            'image' => 'required|file|mimes:png,jpg,jpeg'
        ];
        if ($categoriesId && is_numeric($categoriesId)) {
            $rules['image'] = 'file|mimes:png,jpg,jpeg';
        }
        return $rules;
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
            'file' => ':attribute must be a file',
            'mimes' => ':attribute is not in the correct format(png,jpg,jpeg)'
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
            'name' => 'Name',
            'image' => 'Image'
        ];
    }
}
