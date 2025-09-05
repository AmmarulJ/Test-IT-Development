<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $customers = Customer::active()->get();
        $products = Product::active()->get();
        return view('transactions.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_code' => 'required|exists:customers,customer_code',
            'invoice_date' => 'required|date|before_or_equal:today',
            'details' => 'required|array|min:1|max:50',
            'details.*.product_code' => 'required|exists:products,product_code',
            'details.*.qty' => 'required|integer|min:1|max:9999',
            'details.*.price' => 'required|numeric|min:0|max:999999999.99',
            'details.*.disc1' => 'nullable|numeric|min:0|max:100',
            'details.*.disc2' => 'nullable|numeric|min:0|max:100',
            'details.*.disc3' => 'nullable|numeric|min:0|max:100'
        ], [
            'invoice_date.before_or_equal' => 'Tanggal invoice tidak boleh lebih dari hari ini',
            'details.max' => 'Maksimal 50 item per transaksi',
            'details.*.qty.max' => 'Quantity maksimal 9999',
            'details.*.price.max' => 'Harga maksimal 999,999,999.99'
        ]);

        // Additional validation for duplicate products
        $productCodes = collect($request->details)->pluck('product_code');
        if ($productCodes->count() !== $productCodes->unique()->count()) {
            return back()->withInput()
                ->with('error', 'Tidak boleh ada produk duplikat dalam satu transaksi');
        }

        try {
            DB::beginTransaction();

            // Get customer info
            $customer = Customer::where('customer_code', $request->customer_code)->first();
            if (!$customer) {
                throw new \Exception('Customer tidak ditemukan');
            }
            if (!$customer->is_active) {
                throw new \Exception('Customer tidak aktif');
            }
            
            // Generate invoice number
            $invoiceNo = Transaction::generateInvoiceNumber();

            // Create transaction header
            $transaction = Transaction::create([
                'invoice_no' => $invoiceNo,
                'customer_code' => $customer->customer_code,
                'customer_name' => $customer->customer_name,
                'customer_address' => $customer->full_address_formatted,
                'invoice_date' => $request->invoice_date,
                'status' => 'completed'
            ]);

            $subtotal = 0;
            $totalDiscount = 0;
            $updatedProducts = []; // Track products for rollback

            // Process each detail
            foreach ($request->details as $detail) {
                $product = Product::where('product_code', $detail['product_code'])->first();
                if (!$product) {
                    throw new \Exception("Produk dengan kode {$detail['product_code']} tidak ditemukan");
                }
                if (!$product->is_active) {
                    throw new \Exception("Produk {$product->product_name} tidak aktif");
                }
                
                // Check stock availability
                if ($product->stock < $detail['qty']) {
                    throw new \Exception("Stok produk {$product->product_name} tidak mencukupi. Stok tersedia: {$product->stock}");
                }

                // Calculate discounts
                $price = $detail['price'];
                $disc1 = $detail['disc1'] ?? 0;
                $disc2 = $detail['disc2'] ?? 0;
                $disc3 = $detail['disc3'] ?? 0;

                $netPrice = $price;
                if ($disc1 > 0) {
                    $netPrice = $netPrice - ($netPrice * $disc1 / 100);
                }
                if ($disc2 > 0) {
                    $netPrice = $netPrice - ($netPrice * $disc2 / 100);
                }
                if ($disc3 > 0) {
                    $netPrice = $netPrice - ($netPrice * $disc3 / 100);
                }

                $totalAmount = $netPrice * $detail['qty'];
                $itemDiscount = ($price - $netPrice) * $detail['qty'];

                // Create transaction detail
                TransactionDetail::create([
                    'invoice_no' => $invoiceNo,
                    'product_code' => $detail['product_code'],
                    'product_name' => $product->product_name,
                    'qty' => $detail['qty'],
                    'price' => $price,
                    'disc1' => $disc1,
                    'disc2' => $disc2,
                    'disc3' => $disc3,
                    'net_price' => $netPrice,
                    'total_amount' => $totalAmount
                ]);

                // Update product stock and track for rollback
                $product->decrement('stock', $detail['qty']);
                $updatedProducts[] = [
                    'product' => $product,
                    'qty' => $detail['qty']
                ];

                $subtotal += $totalAmount;
                $totalDiscount += $itemDiscount;
            }

            // Validate total amount
            if ($subtotal > 999999999.99) {
                throw new \Exception('Total transaksi melebihi batas maksimal');
            }

            // Update transaction totals
            $transaction->update([
                'subtotal' => $subtotal,
                'total_discount' => $totalDiscount,
                'total' => $subtotal
            ]);

            DB::commit();

            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Rollback stock updates
            foreach ($updatedProducts as $item) {
                $item['product']->increment('stock', $item['qty']);
            }
            
            return back()->withInput()
                ->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('details');
        return view('transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        try {
            DB::beginTransaction();

            // Restore stock for each detail
            foreach ($transaction->details as $detail) {
                $product = Product::where('product_code', $detail->product_code)->first();
                if ($product) {
                    $product->increment('stock', $detail->qty);
                }
            }

            // Delete transaction (details will be deleted by cascade)
            $transaction->delete();

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dihapus dan stok dikembalikan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}