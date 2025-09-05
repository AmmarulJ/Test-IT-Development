@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Produk</h1>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('products.edit', $product) }}" class="btn-warning">Edit</a>
            @if(!$product->hasTransactions())
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Hapus</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Product Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Produk</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kode Produk</dt>
                    <dd class="text-sm text-gray-900 font-mono">{{ $product->product_code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Produk</dt>
                    <dd class="text-sm text-gray-900">{{ $product->product_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Harga</dt>
                    <dd class="text-sm text-gray-900 font-semibold">{{ $product->formatted_price }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Stok</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($product->stock > 10) bg-green-100 text-green-800
                            @elseif($product->stock > 0) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $product->stock }} unit
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($product->is_active) bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </dd>
                </div>
                @if($product->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                        <dd class="text-sm text-gray-900">{{ $product->description }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                    <dd class="text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Transaction History -->
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Transaksi</h2>
            @php
                $transactions = $product->transactionDetails()->with('transaction')->latest()->take(5)->get();
            @endphp
            
            @if($transactions->count() > 0)
                <div class="space-y-3">
                    @foreach($transactions as $detail)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-sm">{{ $detail->transaction->invoice_no }}</p>
                                    <p class="text-xs text-gray-500">{{ $detail->transaction->formatted_invoice_date }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium">{{ $detail->qty }} unit</p>
                                    <p class="text-xs text-gray-500">{{ $detail->formatted_total_amount }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($product->transactionDetails()->count() > 5)
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        Dan {{ $product->transactionDetails()->count() - 5 }} transaksi lainnya
                    </p>
                @endif
            @else
                <p class="text-gray-500 text-center py-8">Belum ada transaksi untuk produk ini</p>
            @endif
        </div>
    </div>
</div>
@endsection