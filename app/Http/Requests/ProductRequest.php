<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $productId = $this->route('id');
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'discount' => 'nullable|integer|min:0|lte:price',
            'quantity' => 'required|integer|min:0',
            'image' => 'required|image|mimes: jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string',
            'hot' => 'boolean',
            'status' => 'nullable|integer',
            'category_id' => 'required|exists:categories,id',
            'additional_images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ];

        if($productId && is_numeric($productId)){
            $rules['image'] = 'image|mimes:jpg,jpeg,png,gif|max:2048';
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
            'mines' => ':attribute is not in the correct format(png,jpg,jpeg,gif)',
            'discount.lte' => 'The discount cannot be greater than the price.',
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
            'name'=> 'Name',
            'price'=> 'Price',
            'discount'=> 'Discount',
            'quantity'=> 'Quantity',
            'image'=> 'Image',
            'description'=> 'Description',
            'hot'=> 'Hot',
            'status'=> 'Status',
            'category_id'=> 'Category',
            'additional_images' => 'Additional Images',
        ];
    }
}
