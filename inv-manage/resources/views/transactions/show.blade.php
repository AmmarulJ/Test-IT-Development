@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi</h1>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="btn-secondary">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1z"></path>
                </svg>
                Print
            </button>
            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok produk akan dikembalikan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Hapus</button>
            </form>
        </div>
    </div>

    <!-- Invoice -->
    <div class="card print:shadow-none">
        <!-- Invoice Header -->
        <div class="border-b border-gray-200 pb-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">INVOICE</h2>
                    <p class="text-lg font-mono font-semibold text-blue-600">{{ $transaction->invoice_no }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Tanggal Invoice</p>
                    <p class="font-semibold">{{ $transaction->formatted_invoice_date }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Customer</h3>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="font-semibold">{{ $transaction->customer_name }}</p>
                <p class="text-sm text-gray-600">Kode: {{ $transaction->customer_code }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ $transaction->customer_address }}</p>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Detail Transaksi</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Disc 1</th>
                            <th>Disc 2</th>
                            <th>Disc 3</th>
                            <th>Harga Net</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->details as $detail)
                            <tr>
                                <td class="font-mono">{{ $detail->product_code }}</td>
                                <td>{{ $detail->product_name }}</td>
                                <td class="text-center">{{ $detail->qty }}</td>
                                <td class="text-right">{{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $detail->disc1 }}%</td>
                                <td class="text-center">{{ $detail->disc2 }}%</td>
                                <td class="text-center">{{ $detail->disc3 }}%</td>
                                <td class="text-right">{{ number_format($detail->net_price, 0, ',', '.') }}</td>
                                <td class="text-right font-semibold">{{ $detail->formatted_total_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction Summary -->
        <div class="border-t border-gray-200 pt-6">
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal:</span>
                            <span class="text-sm font-medium">{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Diskon:</span>
                            <span class="text-sm font-medium text-red-600">{{ number_format($transaction->total_discount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Total:</span>
                            <span>{{ $transaction->formatted_total }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status and Notes -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-sm text-gray-600">Status: </span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        @if($transaction->status === 'completed') bg-green-100 text-green-800
                        @elseif($transaction->status === 'draft') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
                <div class="text-sm text-gray-500">
                    Dibuat: {{ $transaction->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            
            @if($transaction->notes)
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Catatan:</p>
                    <p class="text-sm text-gray-900">{{ $transaction->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .print\:shadow-none {
        box-shadow: none !important;
    }
    
    nav, .flex.justify-between.items-center, .btn-secondary, .btn-danger {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
}
</style>
@endsection