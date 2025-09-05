<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9]+$/',
                'unique:products,product_code'
            ],
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'product_code.regex' => 'Kode produk hanya boleh mengandung huruf dan angka',
            'product_code.unique' => 'Kode produk sudah digunakan',
            'product_name.required' => 'Nama produk wajib diisi',
            'price.required' => 'Harga wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh kurang dari 0',
            'stock.required' => 'Stok wajib diisi',
            'stock.integer' => 'Stok harus berupa angka bulat',
            'stock.min' => 'Stok tidak boleh kurang dari 0'
        ];
    }
}