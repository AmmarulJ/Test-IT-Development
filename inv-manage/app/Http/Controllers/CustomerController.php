<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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
        ], [
            'customer_code.regex' => 'Kode customer hanya boleh mengandung huruf dan angka',
            'customer_code.unique' => 'Kode customer sudah digunakan'
        ]);

        try {
            Customer::create($request->all());
            return redirect()->route('customers.index')
                ->with('success', 'Customer berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menambahkan customer: ' . $e->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9]+$/',
                Rule::unique('customers')->ignore($customer->id)
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
        ], [
            'customer_code.regex' => 'Kode customer hanya boleh mengandung huruf dan angka',
            'customer_code.unique' => 'Kode customer sudah digunakan'
        ]);

        try {
            $customer->update($request->all());
            return redirect()->route('customers.index')
                ->with('success', 'Customer berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui customer: ' . $e->getMessage());
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            if ($customer->hasTransactions()) {
                return back()->with('error', 'Tidak dapat menghapus customer yang sudah memiliki transaksi');
            }

            $customer->delete();
            return redirect()->route('customers.index')
                ->with('success', 'Customer berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus customer: ' . $e->getMessage());
        }
    }

    public function getCustomer($customer_code)
    {
        $customer = Customer::where('customer_code', $customer_code)->first();
        
        if (!$customer) {
            return response()->json(['error' => 'Customer tidak ditemukan'], 404);
        }
        
        return response()->json([
            'customer_name' => $customer->customer_name,
            'customer_address' => $customer->full_address_formatted
        ]);
    }
}