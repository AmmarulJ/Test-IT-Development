@extends('layouts.app')

@section('title', 'Tambah Customer')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('customers.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Customer</h1>
    </div>

    <!-- Form -->
    <div class="card max-w-4xl">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="customer_code" class="form-label">Kode Customer *</label>
                            <input type="text" 
                                   id="customer_code" 
                                   name="customer_code" 
                                   value="{{ old('customer_code') }}"
                                   class="form-input @error('customer_code') border-red-500 @enderror"
                                   placeholder="Contoh: CUST001"
                                   pattern="[a-zA-Z0-9]+"
                                   title="Hanya huruf dan angka yang diperbolehkan"
                                   required>
                            @error('customer_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Hanya huruf dan angka, tanpa spasi atau karakter khusus</p>
                        </div>

                        <div>
                            <label for="customer_name" class="form-label">Nama Customer *</label>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name" 
                                   value="{{ old('customer_name') }}"
                                   class="form-input @error('customer_name') border-red-500 @enderror"
                                   placeholder="Masukkan nama customer"
                                   required>
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Alamat</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="full_address" class="form-label">Alamat Lengkap *</label>
                            <textarea id="full_address" 
                                      name="full_address" 
                                      rows="3"
                                      class="form-input @error('full_address') border-red-500 @enderror"
                                      placeholder="Jalan, nomor rumah, RT/RW, dll."
                                      required>{{ old('full_address') }}</textarea>
                            @error('full_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="village" class="form-label">Kelurahan *</label>
                                <input type="text" 
                                       id="village" 
                                       name="village" 
                                       value="{{ old('village') }}"
                                       class="form-input @error('village') border-red-500 @enderror"
                                       placeholder="Nama kelurahan"
                                       required>
                                @error('village')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="district" class="form-label">Kecamatan *</label>
                                <input type="text" 
                                       id="district" 
                                       name="district" 
                                       value="{{ old('district') }}"
                                       class="form-input @error('district') border-red-500 @enderror"
                                       placeholder="Nama kecamatan"
                                       required>
                                @error('district')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="form-label">Kota *</label>
                                <input type="text" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city') }}"
                                       class="form-input @error('city') border-red-500 @enderror"
                                       placeholder="Nama kota"
                                       required>
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="province" class="form-label">Provinsi *</label>
                                <input type="text" 
                                       id="province" 
                                       name="province" 
                                       value="{{ old('province') }}"
                                       class="form-input @error('province') border-red-500 @enderror"
                                       placeholder="Nama provinsi"
                                       required>
                                @error('province')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="form-label">Kode Pos *</label>
                                <input type="text" 
                                       id="postal_code" 
                                       name="postal_code" 
                                       value="{{ old('postal_code') }}"
                                       class="form-input @error('postal_code') border-red-500 @enderror"
                                       placeholder="12345"
                                       maxlength="10"
                                       required>
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   class="form-input @error('phone') border-red-500 @enderror"
                                   placeholder="08123456789">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="form-input @error('email') border-red-500 @enderror"
                                   placeholder="customer@example.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('customers.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection