<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 50)->unique();
            $table->string('customer_code', 50);
            $table->string('customer_name');
            $table->text('customer_address');
            $table->date('invoice_date');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'completed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('invoice_no');
            $table->index('customer_code');
            $table->index('invoice_date');
            $table->index('status');
            
            $table->foreign('customer_code')->references('customer_code')->on('customers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};