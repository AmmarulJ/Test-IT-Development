<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9]+$/',
                'unique:customers,customer_code'
            ],
            'customer_name' => 'required|string|max:255',
            'full_address' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'village' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'customer_code.regex' => 'Kode customer hanya boleh mengandung huruf dan angka',
            'customer_code.unique' => 'Kode customer sudah digunakan',
            'customer_name.required' => 'Nama customer wajib diisi',
            'full_address.required' => 'Alamat lengkap wajib diisi',
            'province.required' => 'Provinsi wajib diisi',
            'city.required' => 'Kota wajib diisi',
            'district.required' => 'Kecamatan wajib diisi',
            'village.required' => 'Kelurahan wajib diisi',
            'postal_code.required' => 'Kode pos wajib diisi',
            'email.email' => 'Format email tidak valid'
        ];
    }
}