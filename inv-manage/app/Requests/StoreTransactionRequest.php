<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_code' => 'required|exists:customers,customer_code',
            'invoice_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.product_code' => 'required|exists:products,product_code',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
            'details.*.disc1' => 'nullable|numeric|min:0|max:100',
            'details.*.disc2' => 'nullable|numeric|min:0|max:100',
            'details.*.disc3' => 'nullable|numeric|min:0|max:100'
        ];
    }

    public function messages(): array
    {
        return [
            'customer_code.required' => 'Kode customer wajib diisi',
            'customer_code.exists' => 'Customer tidak ditemukan',
            'invoice_date.required' => 'Tanggal invoice wajib diisi',
            'details.required' => 'Detail transaksi wajib diisi',
            'details.min' => 'Minimal harus ada 1 item',
            'details.*.product_code.required' => 'Kode produk wajib diisi',
            'details.*.product_code.exists' => 'Produk tidak ditemukan',
            'details.*.qty.required' => 'Quantity wajib diisi',
            'details.*.qty.min' => 'Quantity minimal 1',
            'details.*.price.required' => 'Harga wajib diisi',
            'details.*.price.min' => 'Harga tidak boleh kurang dari 0'
        ];
    }
}