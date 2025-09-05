<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'full_address',
        'province',
        'city',
        'district',
        'village',
        'postal_code',
        'phone',
        'email',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_code', 'customer_code');
    }

    public function hasTransactions()
    {
        return $this->transactions()->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullAddressFormattedAttribute()
    {
        return $this->full_address . ', ' . $this->village . ', ' . $this->district . ', ' . 
               $this->city . ', ' . $this->province . ' ' . $this->postal_code;
    }
}