<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 50);
            $table->string('product_code', 50);
            $table->string('product_name');
            $table->integer('qty');
            $table->decimal('price', 15, 2);
            $table->decimal('disc1', 5, 2)->default(0); // percentage
            $table->decimal('disc2', 5, 2)->default(0); // percentage
            $table->decimal('disc3', 5, 2)->default(0); // percentage
            $table->decimal('net_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
            
            $table->index('invoice_no');
            $table->index('product_code');
            
            $table->foreign('invoice_no')->references('invoice_no')->on('transactions')->onDelete('cascade');
            $table->foreign('product_code')->references('product_code')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};