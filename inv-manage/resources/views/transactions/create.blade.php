@extends('layouts.app')

@section('title', 'Buat Transaksi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Buat Transaksi Baru</h1>
    </div>

    <!-- Form -->
    <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
        @csrf
        
        <!-- Transaction Header -->
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Transaksi</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="customer_code" class="form-label">Kode Customer *</label>
                    <select id="customer_code" 
                            name="customer_code" 
                            class="form-input @error('customer_code') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_code }}" 
                                    data-name="{{ $customer->customer_name }}"
                                    data-address="{{ $customer->full_address_formatted }}"
                                    {{ old('customer_code') == $customer->customer_code ? 'selected' : '' }}>
                                {{ $customer->customer_code }} - {{ $customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div id="customer_info" class="mt-2 text-sm text-gray-600 hidden">
                        <p id="customer_name_display"></p>
                        <p id="customer_address_display"></p>
                    </div>
                </div>

                <div>
                    <label for="invoice_date" class="form-label">Tanggal Invoice *</label>
                    <input type="date" 
                           id="invoice_date" 
                           name="invoice_date" 
                           value="{{ old('invoice_date', date('Y-m-d')) }}"
                           class="form-input @error('invoice_date') border-red-500 @enderror"
                           required>
                    @error('invoice_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Detail Transaksi</h2>
                <button type="button" id="addDetailBtn" class="btn-success">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Item
                </button>
            </div>

            <div id="transaction_details">
                <!-- Detail items will be added here -->
            </div>

            <!-- Total Summary -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span id="subtotal_display" class="text-sm font-medium">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Diskon:</span>
                                <span id="discount_display" class="text-sm font-medium text-red-600">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total:</span>
                                <span id="total_display">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('transactions.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Transaksi</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let detailIndex = 0;

// Products data from PHP
const products = @json($products);

// Customer dropdown change
document.getElementById('customer_code').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById('customer_name_display').textContent = selectedOption.dataset.name;
        document.getElementById('customer_address_display').textContent = selectedOption.dataset.address;
        document.getElementById('customer_info').classList.remove('hidden');
    } else {
        document.getElementById('customer_info').classList.add('hidden');
    }
});

// Add detail row
document.getElementById('addDetailBtn').addEventListener('click', function() {
    addDetailRow();
});

function addDetailRow() {
    const detailsContainer = document.getElementById('transaction_details');
    const detailRow = document.createElement('div');
    detailRow.className = 'detail-row border border-gray-200 rounded-lg p-4 mb-4';
    detailRow.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-8 gap-4">
            <div>
                <label class="form-label">Kode Produk *</label>
                <select name="details[${detailIndex}][product_code]" 
                        class="form-input product-code-select"
                        required>
                    <option value="">Pilih Produk</option>
                </select>
                <div class="product-info mt-1 text-xs text-gray-600 hidden">
                    <p class="product-name"></p>
                    <p class="product-stock"></p>
                    <p class="stock-error text-red-600 hidden"></p>
                </div>
            </div>
            <div>
                <label class="form-label">Qty *</label>
                <input type="number" 
                       name="details[${detailIndex}][qty]" 
                       class="form-input qty-input"
                       placeholder="1"
                       min="1"
                       required>
            </div>
            <div>
                <label class="form-label">Harga *</label>
                <input type="number" 
                       name="details[${detailIndex}][price]" 
                       class="form-input price-input"
                       placeholder="0"
                       min="0"
                       step="0.01"
                       required>
            </div>
            <div>
                <label class="form-label">Disc 1 (%)</label>
                <input type="number" 
                       name="details[${detailIndex}][disc1]" 
                       class="form-input disc-input"
                       placeholder="0"
                       min="0"
                       max="100"
                       step="0.01">
            </div>
            <div>
                <label class="form-label">Disc 2 (%)</label>
                <input type="number" 
                       name="details[${detailIndex}][disc2]" 
                       class="form-input disc-input"
                       placeholder="0"
                       min="0"
                       max="100"
                       step="0.01">
            </div>
            <div>
                <label class="form-label">Disc 3 (%)</label>
                <input type="number" 
                       name="details[${detailIndex}][disc3]" 
                       class="form-input disc-input"
                       placeholder="0"
                       min="0"
                       max="100"
                       step="0.01">
            </div>
            <div>
                <label class="form-label">Total</label>
                <input type="text" 
                       class="form-input total-display bg-gray-50"
                       placeholder="Rp 0"
                       readonly>
            </div>
            <div class="flex items-end">
                <button type="button" class="btn-danger remove-detail-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    detailsContainer.appendChild(detailRow);
    
    // Add event listeners for the new row
    setupDetailRowEvents(detailRow);
    
    // Update all product dropdowns
    updateProductDropdowns();
    
    detailIndex++;
}

function setupDetailRowEvents(row) {
    const productCodeSelect = row.querySelector('.product-code-select');
    const qtyInput = row.querySelector('.qty-input');
    const priceInput = row.querySelector('.price-input');
    const discInputs = row.querySelectorAll('.disc-input');
    const totalDisplay = row.querySelector('.total-display');
    const removeBtn = row.querySelector('.remove-detail-btn');
    
    // Product dropdown change
    productCodeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            row.querySelector('.product-name').textContent = selectedOption.dataset.name;
            row.querySelector('.product-stock').textContent = `Stok: ${selectedOption.dataset.stock}`;
            row.querySelector('.product-info').classList.remove('hidden');
            priceInput.value = selectedOption.dataset.price;
            
            // Store stock data for validation
            row.dataset.availableStock = selectedOption.dataset.stock;
            
            // Validate current qty
            validateStock(row);
            calculateRowTotal(row);
        } else {
            row.querySelector('.product-info').classList.add('hidden');
            priceInput.value = '';
            clearStockValidation(row);
        }
        
        // Update all product dropdowns to prevent duplicates
        updateProductDropdowns();
    });
    
    // Stock validation on qty change
    qtyInput.addEventListener('input', function() {
        validateStock(row);
        calculateRowTotal(row);
    });
    
    // Calculate total when inputs change (excluding qty which is handled separately)
    [priceInput, ...discInputs].forEach(input => {
        input.addEventListener('input', () => calculateRowTotal(row));
    });
    
    // Remove row
    removeBtn.addEventListener('click', function() {
        row.remove();
        calculateGrandTotal();
        updateProductDropdowns();
    });
}

function calculateRowTotal(row) {
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const disc1 = parseFloat(row.querySelector('input[name*="[disc1]"]').value) || 0;
    const disc2 = parseFloat(row.querySelector('input[name*="[disc2]"]').value) || 0;
    const disc3 = parseFloat(row.querySelector('input[name*="[disc3]"]').value) || 0;
    
    let netPrice = price;
    
    // Apply cascading discounts
    if (disc1 > 0) {
        netPrice = netPrice - (netPrice * disc1 / 100);
    }
    if (disc2 > 0) {
        netPrice = netPrice - (netPrice * disc2 / 100);
    }
    if (disc3 > 0) {
        netPrice = netPrice - (netPrice * disc3 / 100);
    }
    
    const total = netPrice * qty;
    
    row.querySelector('.total-display').value = formatCurrency(total);
    
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let subtotal = 0;
    let totalDiscount = 0;
    
    document.querySelectorAll('.detail-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const disc1 = parseFloat(row.querySelector('input[name*="[disc1]"]').value) || 0;
        const disc2 = parseFloat(row.querySelector('input[name*="[disc2]"]').value) || 0;
        const disc3 = parseFloat(row.querySelector('input[name*="[disc3]"]').value) || 0;
        
        let netPrice = price;
        
        // Apply cascading discounts
        if (disc1 > 0) {
            netPrice = netPrice - (netPrice * disc1 / 100);
        }
        if (disc2 > 0) {
            netPrice = netPrice - (netPrice * disc2 / 100);
        }
        if (disc3 > 0) {
            netPrice = netPrice - (netPrice * disc3 / 100);
        }
        
        const itemTotal = netPrice * qty;
        const itemDiscount = (price - netPrice) * qty;
        
        subtotal += itemTotal;
        totalDiscount += itemDiscount;
    });
    
    document.getElementById('subtotal_display').textContent = formatCurrency(subtotal);
    document.getElementById('discount_display').textContent = formatCurrency(totalDiscount);
    document.getElementById('total_display').textContent = formatCurrency(subtotal);
}

function formatCurrency(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

// Stock validation functions
function validateStock(row) {
    const qtyInput = row.querySelector('.qty-input');
    const availableStock = parseInt(row.dataset.availableStock) || 0;
    const requestedQty = parseInt(qtyInput.value) || 0;
    const stockError = row.querySelector('.stock-error');
    
    if (availableStock > 0 && requestedQty > availableStock) {
        stockError.textContent = `Stok tidak mencukupi! Tersedia: ${availableStock}`;
        stockError.classList.remove('hidden');
        qtyInput.classList.add('border-red-500');
        qtyInput.setCustomValidity(`Stok tidak mencukupi. Tersedia: ${availableStock}`);
    } else {
        stockError.classList.add('hidden');
        qtyInput.classList.remove('border-red-500');
        qtyInput.setCustomValidity('');
    }
}

function clearStockValidation(row) {
    const stockError = row.querySelector('.stock-error');
    const qtyInput = row.querySelector('.qty-input');
    
    stockError.classList.add('hidden');
    qtyInput.classList.remove('border-red-500');
    qtyInput.setCustomValidity('');
    row.dataset.availableStock = '0';
}

// Function to update product dropdown options
function updateProductDropdowns() {
    const allRows = document.querySelectorAll('.detail-row');
    const allSelectedProducts = [];
    
    // Collect all selected products
    allRows.forEach(row => {
        const select = row.querySelector('.product-code-select');
        if (select.value) {
            allSelectedProducts.push(select.value);
        }
    });
    
    // Update all dropdowns
    allRows.forEach(row => {
        const select = row.querySelector('.product-code-select');
        const currentValue = select.value;
        
        // Clear and rebuild options
        select.innerHTML = '<option value="">Pilih Produk</option>';
        
        // Add products from PHP data (available in global scope)
        if (typeof products !== 'undefined') {
            products.forEach(product => {
                const isSelected = allSelectedProducts.includes(product.product_code) && product.product_code !== currentValue;
                if (!isSelected) {
                    const option = document.createElement('option');
                    option.value = product.product_code;
                    option.dataset.name = product.product_name;
                    option.dataset.price = product.price;
                    option.dataset.stock = product.stock;
                    option.textContent = `${product.product_code} - ${product.product_name} (Stok: ${product.stock})`;
                    select.appendChild(option);
                }
            });
        }
        
        // Restore current selection
        if (currentValue) {
            select.value = currentValue;
        }
    });
}

// Add first detail row on page load
document.addEventListener('DOMContentLoaded', function() {
    addDetailRow();
    
    // Initialize customer info display if customer is pre-selected
    const customerSelect = document.getElementById('customer_code');
    if (customerSelect.value) {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        document.getElementById('customer_name_display').textContent = selectedOption.dataset.name;
        document.getElementById('customer_address_display').textContent = selectedOption.dataset.address;
        document.getElementById('customer_info').classList.remove('hidden');
    }
});
</script>
@endpush
@endsection