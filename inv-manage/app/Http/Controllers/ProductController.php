<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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
        ], [
            'product_code.regex' => 'Kode produk hanya boleh mengandung huruf dan angka',
            'product_code.unique' => 'Kode produk sudah digunakan'
        ]);

        try {
            Product::create($request->all());
            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9]+$/',
                Rule::unique('products')->ignore($product->id)
            ],
            'product_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ], [
            'product_code.regex' => 'Kode produk hanya boleh mengandung huruf dan angka',
            'product_code.unique' => 'Kode produk sudah digunakan'
        ]);

        try {
            $product->update($request->all());
            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->hasTransactions()) {
                return back()->with('error', 'Tidak dapat menghapus produk yang sudah memiliki transaksi');
            }

            $product->delete();
            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function getProduct(Request $request)
    {
        $product = Product::where('product_code', $request->product_code)->first();
        
        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }
        
        return response()->json([
            'product_name' => $product->product_name,
            'price' => $product->price,
            'stock' => $product->stock
        ]);
    }
}