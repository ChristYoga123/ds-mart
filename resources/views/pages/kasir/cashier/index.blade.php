@extends('layouts.app')

@section('content')
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Section Tabel Produk -->
        <div
            class="w-full lg:w-3/5 bg-white rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-transform duration-300">
            <h2 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center">
                <i class="fas fa-box-open mr-3"></i>
                Daftar Produk
            </h2>
            <div class="overflow-x-auto rounded-xl">
                <table id="product-table" class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs uppercase bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl font-semibold tracking-wide">Barcode</th>
                            <th class="px-4 py-4 font-semibold tracking-wide">Nama Produk</th>
                            <th class="px-6 py-4 font-semibold tracking-wide">Stok</th>
                            <th class="px-6 py-4 font-semibold tracking-wide">Harga</th>
                            <th class="px-6 py-4 rounded-tr-xl font-semibold tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Data produk akan diisi secara dinamis -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Cart -->
        <div
            class="w-full lg:w-2/5 bg-white rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-transform duration-300">
            <h2 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center">
                <i class="fas fa-shopping-cart mr-3"></i>
                Keranjang Belanja
            </h2>

            <!-- Informasi Transaksi -->
            <div class="mb-6 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-100">

                <div class="flex justify-between">
                    <span class="font-medium text-indigo-700">Kasir:</span>
                    <span class="font-bold text-purple-600">{{ ucwords(Auth::user()->name) }}</span>
                </div>
            </div>

            <!-- Daftar Barang di Keranjang -->
            <div class="mb-6 max-h-64 overflow-y-auto rounded-xl border border-gray-100">
                <table id="cart-table" class="w-full text-sm">
                    <thead class="text-xs uppercase bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-center">Qty</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-items">
                        <!-- Item keranjang akan diisi secara dinamis -->
                    </tbody>
                </table>
            </div>

            <!-- Total dan Pembayaran -->
            <div class="space-y-4 border-t border-gray-100 pt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                    <span class="text-indigo-700">Total:</span>
                    <span id="total"
                        class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Rp
                        0</span>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div class="mt-6 space-y-4">
                <div>
                    <label class="block text-gray-700 mb-2 font-medium" for="payment-amount">Jumlah Bayar</label>
                    <input type="number" id="payment-amount"
                        class="w-full p-3 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:outline-none transition-colors"
                        placeholder="Masukkan jumlah pembayaran">
                </div>
                <div class="flex items-center p-3 bg-green-50 rounded-xl">
                    <span class="text-gray-700 mr-2">Kembalian:</span>
                    <span id="change-amount" class="font-bold text-green-600">Rp 0</span>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <button id="reset-btn"
                        class="py-3 px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition duration-200 font-medium flex items-center justify-center cursor-pointer">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </button>
                    <button id="pay-btn" onclick="bayar()"
                        class="py-3 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-xl transition duration-200 flex items-center justify-center cursor-pointer">
                        <i class="fas fa-credit-card mr-2"></i>
                        Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Format currency to IDR
        function formatCurrency(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
        }

        // Cart data
        let cartItems = [];
        let searchTimeout;
        let currentTotal = 0; // Tambahkan variabel untuk menyimpan total mentah

        // Initialize DataTable
        $(document).ready(function() {
            // Populate product table
            const productTable = $('#product-table').DataTable({
                ajax: "{{ route('kasir.index') }}",
                processing: true,
                serverSide: true,
                columns: [{
                        data: 'barcode',
                        className: 'px-6 py-4 font-medium text-gray-900'
                    },
                    {
                        data: 'nama',
                        className: 'px-4 py-4'
                    },
                    {
                        data: 'stok',
                        className: 'px-6 py-4'
                    },
                    {
                        data: 'harga_jual_per_pcs',
                        className: 'px-6 py-4 font-medium',
                        render: function(data) {
                            return formatCurrency(data);
                        }
                    },
                    {
                        data: 'aksi',
                        orderable: false
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        next: '<i class="fas fa-angle-right"></i>',
                        previous: '<i class="fas fa-angle-left"></i>'
                    }
                },
                dom: '<"flex flex-col sm:flex-row justify-between items-center gap-4 p-4 bg-white rounded-xl border border-indigo-100 mb-4"<"flex items-center gap-2"l><"flex items-center gap-2"f>>rt<"flex flex-col sm:flex-row justify-between items-center gap-4 p-4 bg-white rounded-xl border border-indigo-100 mt-4"<"text-sm text-gray-600"i><"dataTables_paginate"p>>',
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                pageLength: 10,
                responsive: true,
                order: [
                    [0, 'asc']
                ],
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass('hover:bg-gray-50 transition-colors duration-200');
                }
            });

            // Untuk menyimpan search string saat typing langsung
            let directSearchString = '';
            let directSearchTimer;

            // Ambil referensi search input untuk digunakan di seluruh kode
            const $searchInput = $('.dataTables_filter input');

            // Improved keyboard handler
            $(document).on('keydown', function(e) {
                // Skip if we're in any input field
                if ($(e.target).is('input, textarea, select') || e.ctrlKey || e.altKey || e.metaKey) {
                    // Special case for Ctrl+F/Cmd+F
                    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                        e.preventDefault();
                        $searchInput.focus().select(); // Focus and select all text
                    }
                    return;
                }

                // Handle Escape key
                if (e.key === 'Escape') {
                    $searchInput.val('').trigger('input');
                    directSearchString = '';
                    productTable.search('').draw();
                    return;
                }

                // For any alphanumeric key, space, or other printable character when not already in an input
                if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    e.preventDefault();

                    // Append the key to our direct search string
                    directSearchString += e.key;

                    // Update the search box with the current string
                    $searchInput.val(directSearchString).focus();

                    // Clear any existing timer
                    clearTimeout(directSearchTimer);

                    // Set a new timer to clear the direct search string after a period of inactivity
                    directSearchTimer = setTimeout(() => {
                        directSearchString = '';
                    }, 1500); // Reset after 1.5 seconds of no typing

                    // Trigger the search with debounce
                    triggerSearch();
                }
            });

            // Debounced search function
            function triggerSearch() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    productTable.search($searchInput.val()).draw();
                }, 300); // Reduce delay to 300ms for better responsiveness
            }

            // Input handler for the search box
            $searchInput.off('input').on('input', function() {
                directSearchString = $(this).val(); // Keep the direct search string in sync
                triggerSearch();
            });

            // Handle backspace key specially
            $searchInput.on('keydown', function(e) {
                if (e.key === 'Backspace') {
                    directSearchString = $(this).val();
                    // No need to trigger search here, the input event will handle it
                }
            });

            // Add to cart button click - menggunakan event delegation
            $('#product-table tbody').on('click', '.add-to-cart', function() {
                const data = productTable.row($(this).parents('tr')).data();
                addToCart(data);
            });

            // Calculate change on payment input change
            $('#payment-amount').on('input', function() {
                calculateChange();
            });

            // Reset button click
            $('#reset-btn').on('click', function() {
                resetTransaction();
            });

            // Initialize cart event handlers
            $(document).on('click', '.increase-qty', function(e) {
                e.preventDefault();
                const itemId = $(this).data('id');
                increaseQuantity(itemId);
            });

            $(document).on('click', '.decrease-qty', function(e) {
                e.preventDefault();
                const itemId = $(this).data('id');
                decreaseQuantity(itemId);
            });

            $(document).on('click', '.remove-item', function(e) {
                e.preventDefault();
                const itemId = $(this).data('id');
                removeCartItem(itemId);
            });

            $(document).on('input', '.qty-input', function() {
                handleQuantityInput($(this));
            });

            $(document).on('paste', '.qty-input', function(e) {
                handleQuantityPaste(e, $(this));
            });
        });

        // Function untuk increase quantity
        function increaseQuantity(itemId) {
            const item = cartItems.find(i => i.id == itemId);
            if (!item) return;

            if (item.quantity >= item.stok) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning(`Stok ${item.name} hanya tersedia ${item.stok} pcs`);
                } else {
                    alert(`Stok ${item.name} hanya tersedia ${item.stok} pcs`);
                }
                return;
            }

            item.quantity += 1;
            item.subtotal = item.quantity * item.price;
            updateCartDisplay();
        }

        // Function untuk decrease quantity
        function decreaseQuantity(itemId) {
            const item = cartItems.find(i => i.id == itemId);
            if (!item) return;

            if (item.quantity > 1) {
                item.quantity -= 1;
                item.subtotal = item.quantity * item.price;
            } else {
                cartItems = cartItems.filter(i => i.id != itemId);
            }
            updateCartDisplay();
        }

        // Function untuk remove item
        function removeCartItem(itemId) {
            cartItems = cartItems.filter(i => i.id != itemId);
            updateCartDisplay();
        }

        // Function untuk handle quantity input
        function handleQuantityInput($input) {
            $input.val($input.val().replace(/[^0-9]/g, ''));

            const itemId = $input.data('id');
            const item = cartItems.find(i => i.id === itemId);
            if (!item) return;

            const newQty = parseInt($input.val()) || 1;
            const maxStok = parseInt($input.data('stok')) || item.stok;

            if (newQty > maxStok) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning(`Stok ${item.name} hanya tersedia ${maxStok} pcs`);
                } else {
                    alert(`Stok ${item.name} hanya tersedia ${maxStok} pcs`);
                }
                $input.val(maxStok);
                item.quantity = maxStok;
            } else if (newQty > 0) {
                item.quantity = newQty;
            } else {
                item.quantity = 1;
                $input.val(1);
            }

            item.subtotal = item.quantity * item.price;
            updateCartDisplay();
        }

        // Function untuk handle quantity paste
        function handleQuantityPaste(e, $input) {
            e.preventDefault();
            const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            if (!/^\d+$/.test(pastedText)) return;

            const itemId = $input.data('id');
            const item = cartItems.find(i => i.id === itemId);
            if (!item) return;

            const newQty = parseInt(pastedText);
            const maxStok = parseInt($input.data('stok')) || item.stok;

            if (newQty > maxStok) {
                if (typeof toastr !== 'undefined') {
                    toastr.warning(`Stok ${item.name} hanya tersedia ${maxStok} pcs`);
                } else {
                    alert(`Stok ${item.name} hanya tersedia ${maxStok} pcs`);
                }
                $input.val(maxStok);
                item.quantity = maxStok;
            } else if (newQty > 0) {
                $input.val(newQty);
                item.quantity = newQty;
            } else {
                $input.val(1);
                item.quantity = 1;
            }

            item.subtotal = item.quantity * item.price;
            updateCartDisplay();
        }

        // Add item to cart
        function addToCart(product) {
            const existingItem = cartItems.find(item => item.id === product.barcode);

            if (existingItem) {
                if (existingItem.quantity >= product.stok) {
                    if (typeof toastr !== 'undefined') {
                        toastr.warning(`Stok ${product.nama} hanya tersedia ${product.stok} pcs`);
                    } else {
                        alert(`Stok ${product.nama} hanya tersedia ${product.stok} pcs`);
                    }
                    return;
                }
                existingItem.quantity += 1;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                cartItems.push({
                    id: product.barcode,
                    name: product.nama,
                    price: parseFloat(product.harga_jual_per_pcs), // Ensure numeric value
                    quantity: 1,
                    subtotal: parseFloat(product.harga_jual_per_pcs), // Ensure numeric value
                    stok: product.stok
                });
            }
            updateCartDisplay();
        }

        // Update cart display
        function updateCartDisplay() {
            const $cartItemsContainer = $('#cart-items');
            $cartItemsContainer.empty();

            let subtotal = 0;

            cartItems.forEach(item => {
                const $row = $('<tr>').addClass('border-b border-gray-100');

                $row.html(`
        <td class="px-3 py-2">
            <div>
                <div class="font-medium">${item.name}</div>
                <div class="text-xs text-gray-500">${formatCurrency(item.price)}</div>
            </div>
        </td>
        <td class="px-3 py-2 text-center">
            <div class="flex items-center justify-center gap-2">
                <button type="button" class="decrease-qty text-gray-500 hover:text-red-500 cursor-pointer" data-id="${item.id}">
                    <i class="fas fa-minus-circle"></i>
                </button>
                <input type="text" 
                    class="qty-input w-12 text-center border border-gray-200 rounded-lg px-1 py-1 focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:outline-none" 
                    value="${item.quantity}" 
                    data-id="${item.id}"
                    data-price="${item.price}"
                    data-stok="${item.stok}">
                <button type="button" class="increase-qty text-gray-500 hover:text-green-500 cursor-pointer" data-id="${item.id}">
                    <i class="fas fa-plus-circle"></i>
                </button>
            </div>
        </td>
        <td class="px-3 py-2 text-right font-medium">${formatCurrency(item.subtotal)}</td>
        <td class="px-3 py-2 text-center">
            <button class="remove-item text-red-500 hover:text-red-700 cursor-pointer" data-id="${item.id}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `);

                $cartItemsContainer.append($row);
                subtotal += parseFloat(item.subtotal); // Ensure numeric addition
            });

            // If cart is empty, show message
            if (cartItems.length === 0) {
                $cartItemsContainer.html(`
        <tr>
            <td colspan="4" class="px-3 py-4 text-center text-gray-500">
                Keranjang belanja kosong
            </td>
        </tr>
    `);
            }

            // Update totals - simpan nilai mentah ke variabel global
            currentTotal = subtotal;
            $('#total').text(formatCurrency(currentTotal));

            // Recalculate change if payment amount is filled
            calculateChange();
        }

        // Calculate change - gunakan variabel global currentTotal
        function calculateChange() {
            const paymentAmount = parseFloat($('#payment-amount').val()) || 0;
            const total = currentTotal; // Gunakan variabel global, bukan parsing dari text

            const change = paymentAmount - total;
            $('#change-amount').text(change >= 0 ? formatCurrency(change) : 'Rp 0');

            // Enable/disable pay button based on payment amount
            const $payBtn = $('#pay-btn');
            if (paymentAmount >= total && total > 0) {
                $payBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
            } else {
                $payBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            }
        }

        // Reset transaction
        function resetTransaction() {
            cartItems = [];
            currentTotal = 0; // Reset total mentah
            $('#payment-amount').val('');
            updateCartDisplay();

            // Generate new transaction ID if element exists
            if ($('#transaction-id').length) {
                const date = new Date();
                const formattedDate =
                    date.getFullYear().toString() +
                    (date.getMonth() + 1).toString().padStart(2, '0') +
                    date.getDate().toString().padStart(2, '0');

                const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                $('#transaction-id').text(`TRX-${formattedDate}-${randomNum}`);
            }
        }

        function bayar() {
            $.ajax({
                url: "{{ route('kasir.bayar') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    cart: cartItems,
                    total_bayar: $('#payment-amount').val()
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.success);
                        } else {
                            alert(response.success);
                        }
                        resetTransaction();
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.error);
                        } else {
                            alert(response.error);
                        }
                    }
                    $('#product-table').DataTable().ajax.reload();
                },
                error: function(xhr, status, error) {
                    const errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error :
                        'Terjadi kesalahan saat memproses pembayaran';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                    $('#product-table').DataTable().ajax.reload();
                }
            });
        }
    </script>
@endpush
