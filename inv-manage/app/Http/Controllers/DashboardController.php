<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
            'total_transactions' => Transaction::count(),
            'low_stock_products' => Product::where('stock', '<=', 5)->count(),
            'recent_transactions' => Transaction::with('customer')
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('dashboard', compact('stats'));
    }
}