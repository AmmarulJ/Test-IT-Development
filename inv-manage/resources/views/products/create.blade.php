@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Produk</h1>
    </div>

    <!-- Form -->
    <div class="card max-w-2xl">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="product_code" class="form-label">Kode Produk *</label>
                    <input type="text" 
                           id="product_code" 
                           name="product_code" 
                           value="{{ old('product_code') }}"
                           class="form-input @error('product_code') border-red-500 @enderror"
                           placeholder="Contoh: PRD001"
                           pattern="[a-zA-Z0-9]+"
                           title="Hanya huruf dan angka yang diperbolehkan"
                           required>
                    @error('product_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Hanya huruf dan angka, tanpa spasi atau karakter khusus</p>
                </div>

                <div>
                    <label for="product_name" class="form-label">Nama Produk *</label>
                    <input type="text" 
                           id="product_name" 
                           name="product_name" 
                           value="{{ old('product_name') }}"
                           class="form-input @error('product_name') border-red-500 @enderror"
                           placeholder="Masukkan nama produk"
                           required>
                    @error('product_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="form-label">Harga *</label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="{{ old('price') }}"
                               class="form-input @error('price') border-red-500 @enderror"
                               placeholder="0"
                               min="0"
                               step="0.01"
                               required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="form-label">Stok *</label>
                        <input type="number" 
                               id="stock" 
                               name="stock" 
                               value="{{ old('stock') }}"
                               class="form-input @error('stock') border-red-500 @enderror"
                               placeholder="0"
                               min="0"
                               required>
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="form-input @error('description') border-red-500 @enderror"
                              placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('products.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection