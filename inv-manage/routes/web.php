<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});

// Product routes
Route::resource('products', ProductController::class);
Route::get('/api/products/{product_code}', [ProductController::class, 'getProduct']);

// Customer routes
Route::resource('customers', CustomerController::class);
Route::get('/api/customers/{customer_code}', [CustomerController::class, 'getCustomer']);

// Transaction routes
Route::resource('transactions', TransactionController::class)->except(['edit', 'update']);