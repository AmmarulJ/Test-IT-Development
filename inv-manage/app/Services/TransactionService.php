<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            // Get customer info
            $customer = Customer::where('customer_code', $data['customer_code'])->firstOrFail();
            
            // Generate invoice number
            $invoiceNo = Transaction::generateInvoiceNumber();

            // Create transaction header
            $transaction = Transaction::create([
                'invoice_no' => $invoiceNo,
                'customer_code' => $customer->customer_code,
                'customer_name' => $customer->customer_name,
                'customer_address' => $customer->full_address_formatted,
                'invoice_date' => $data['invoice_date'],
                'status' => 'completed'
            ]);

            $subtotal = 0;
            $totalDiscount = 0;

            // Process each detail
            foreach ($data['details'] as $detail) {
                $product = Product::where('product_code', $detail['product_code'])->firstOrFail();
                
                // Check stock availability
                if ($product->stock < $detail['qty']) {
                    throw new \Exception("Stok produk {$product->product_name} tidak mencukupi. Stok tersedia: {$product->stock}");
                }

                // Calculate discounts
                $price = $detail['price'];
                $disc1 = $detail['disc1'] ?? 0;
                $disc2 = $detail['disc2'] ?? 0;
                $disc3 = $detail['disc3'] ?? 0;

                $netPrice = $this->calculateNetPrice($price, $disc1, $disc2, $disc3);
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

                // Update product stock
                $product->decrement('stock', $detail['qty']);

                $subtotal += $totalAmount;
                $totalDiscount += $itemDiscount;
            }

            // Update transaction totals
            $transaction->update([
                'subtotal' => $subtotal,
                'total_discount' => $totalDiscount,
                'total' => $subtotal
            ]);

            return $transaction;
        });
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Restore stock for each detail
            foreach ($transaction->details as $detail) {
                $product = Product::where('product_code', $detail->product_code)->first();
                if ($product) {
                    $product->increment('stock', $detail->qty);
                }
            }

            // Delete transaction (details will be deleted by cascade)
            $transaction->delete();
        });
    }

    private function calculateNetPrice(float $price, float $disc1, float $disc2, float $disc3): float
    {
        $netPrice = $price;
        
        // Apply cascading discounts
        if ($disc1 > 0) {
            $netPrice = $netPrice - ($netPrice * $disc1 / 100);
        }
        
        if ($disc2 > 0) {
            $netPrice = $netPrice - ($netPrice * $disc2 / 100);
        }
        
        if ($disc3 > 0) {
            $netPrice = $netPrice - ($netPrice * $disc3 / 100);
        }
        
        return round($netPrice, 2);
    }
}