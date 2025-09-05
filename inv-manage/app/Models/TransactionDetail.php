<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'product_code',
        'product_name',
        'qty',
        'price',
        'disc1',
        'disc2',
        'disc3',
        'net_price',
        'total_amount'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'disc1' => 'decimal:2',
        'disc2' => 'decimal:2',
        'disc3' => 'decimal:2',
        'net_price' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'invoice_no', 'invoice_no');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    public function calculateNetPrice()
    {
        $price = $this->price;
        
        // Apply cascading discounts
        if ($this->disc1 > 0) {
            $price = $price - ($price * $this->disc1 / 100);
        }
        
        if ($this->disc2 > 0) {
            $price = $price - ($price * $this->disc2 / 100);
        }
        
        if ($this->disc3 > 0) {
            $price = $price - ($price * $this->disc3 / 100);
        }
        
        return round($price, 2);
    }

    public function calculateTotalAmount()
    {
        return $this->net_price * $this->qty;
    }

    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }
}