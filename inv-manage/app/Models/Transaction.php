<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'customer_code',
        'customer_name',
        'customer_address',
        'invoice_date',
        'subtotal',
        'total_discount',
        'total',
        'status',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_code', 'customer_code');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'invoice_no', 'invoice_no');
    }

    public static function generateInvoiceNumber()
    {
        $now = Carbon::now();
        $yearMonth = $now->format('ym'); // 2407 for July 2024
        $prefix = "INV/{$yearMonth}/";
        
        // Get the last invoice number for this month
        $lastInvoice = self::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('invoice_no', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedInvoiceDateAttribute()
    {
        return $this->invoice_date->format('d/m/Y');
    }
}