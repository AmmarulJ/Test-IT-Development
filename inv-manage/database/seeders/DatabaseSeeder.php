<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Customer;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample products
        Product::create([
            'product_code' => 'PRD001',
            'product_name' => 'Laptop ASUS ROG',
            'price' => 15000000,
            'stock' => 10,
            'description' => 'Laptop gaming high performance'
        ]);

        Product::create([
            'product_code' => 'PRD002',
            'product_name' => 'Mouse Gaming Logitech',
            'price' => 500000,
            'stock' => 25,
            'description' => 'Mouse gaming dengan DPI tinggi'
        ]);

        Product::create([
            'product_code' => 'PRD003',
            'product_name' => 'Keyboard Mechanical',
            'price' => 750000,
            'stock' => 15,
            'description' => 'Keyboard mechanical RGB'
        ]);

        // Create sample customers
        Customer::create([
            'customer_code' => 'CUST001',
            'customer_name' => 'PT. Teknologi Maju',
            'full_address' => 'Jl. Sudirman No. 123, RT 01/RW 02',
            'village' => 'Senayan',
            'district' => 'Kebayoran Baru',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12190',
            'phone' => '021-12345678',
            'email' => 'info@teknologimaju.com'
        ]);

        Customer::create([
            'customer_code' => 'CUST002',
            'customer_name' => 'CV. Berkah Jaya',
            'full_address' => 'Jl. Gatot Subroto No. 456, RT 03/RW 04',
            'village' => 'Kuningan',
            'district' => 'Setiabudi',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postal_code' => '12950',
            'phone' => '021-87654321',
            'email' => 'admin@berkahjaya.co.id'
        ]);
    }
}