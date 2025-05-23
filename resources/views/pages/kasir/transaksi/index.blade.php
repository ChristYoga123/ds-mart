@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Widget Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Transaksi Hari Ini -->
            <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-transform duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Transaksi Hari Ini</p>
                        <h3 class="text-2xl font-bold text-indigo-600 mt-1" id="total-transaksi-hari-ini">0</h3>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-xl">
                        <i class="fas fa-shopping-cart text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan Hari Ini -->
            <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-transform duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pendapatan Hari Ini</p>
                        <h3 class="text-2xl font-bold text-green-600 mt-1" id="total-pendapatan-hari-ini">Rp 0</h3>
                    </div>
                    <div class="p-3 bg-green-100 rounded-xl">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Transaksi Bulan Ini -->
            <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-transform duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Transaksi Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-purple-600 mt-1" id="total-transaksi-bulan-ini">0</h3>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-xl">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Pendapatan Bulan Ini -->
            <div class="bg-white rounded-2xl shadow-lg p-6 transform hover:scale-[1.01] transition-transform duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pendapatan Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1" id="total-pendapatan-bulan-ini">Rp 0</h3>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Transaksi -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center">
                <i class="fas fa-history mr-3"></i>
                Riwayat Transaksi
            </h2>
            <div class="overflow-x-auto rounded-xl">
                <table id="transaksi-table" class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs uppercase bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl font-semibold tracking-wide">Kode Transaksi</th>
                            <th class="px-4 py-4 font-semibold tracking-wide">Tanggal</th>
                            <th class="px-6 py-4 font-semibold tracking-wide">Total Bayar</th>
                            <th class="px-6 py-4 rounded-tr-xl font-semibold tracking-wide">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Data akan diisi secara dinamis -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div id="detail-modal" class="fixed inset-0 bg-white/30 backdrop-blur-sm hidden items-center justify-center">
        <div class="bg-white rounded-2xl p-6 w-full max-w-2xl mx-4 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-indigo-700">Detail Transaksi</h3>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="detail-content" class="space-y-4">
                <!-- Detail transaksi akan diisi secara dinamis -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Format currency to IDR
            function formatCurrency(amount) {
                return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
            }

            // Initialize DataTable
            const transaksiTable = $('#transaksi-table').DataTable({
                ajax: "{{ route('kasir.transaksi.data') }}",
                processing: true,
                serverSide: true,
                columns: [{
                        data: 'kode_transaksi',
                        className: 'px-6 py-4 font-medium text-gray-900'
                    },
                    {
                        data: 'created_at',
                        className: 'px-4 py-4',
                        render: function(data) {
                            const date = new Date(data);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            const hours = String(date.getHours()).padStart(2, '0');
                            const minutes = String(date.getMinutes()).padStart(2, '0');
                            return `${day}/${month}/${year} ${hours}:${minutes}`;
                        }
                    },
                    {
                        data: 'total_bayar',
                        className: 'px-6 py-4 font-medium',
                        render: function(data) {
                            return formatCurrency(data);
                        }
                    },
                    {
                        data: null,
                        className: 'px-6 py-4',
                        render: function(data) {
                            return `<button onclick="showDetail('${data.kode_transaksi}')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-eye mr-2"></i>Detail
                        </button>`;
                        }
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
                    [1, 'desc']
                ]
            });

            // Load widget data
            function loadWidgetData() {
                $.get("{{ route('kasir.transaksi.widget') }}", function(data) {
                    $('#total-transaksi-hari-ini').text(data.total_transaksi_hari_ini);
                    $('#total-pendapatan-hari-ini').text(formatCurrency(data.total_pendapatan_hari_ini));
                    $('#total-transaksi-bulan-ini').text(data.total_transaksi_bulan_ini);
                    $('#total-pendapatan-bulan-ini').text(formatCurrency(data.total_pendapatan_bulan_ini));
                });
            }

            // Load widget data on page load
            loadWidgetData();

            // Refresh widget data every 5 minutes
            setInterval(loadWidgetData, 300000);
        });

        // Show detail modal
        function showDetail(kodeTransaksi) {
            $.get(`/kasir/transaksi/detail/${kodeTransaksi}`, function(data) {
                const date = new Date(data.created_at);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const formattedDate = `${day}/${month}/${year} ${hours}:${minutes}`;

                let detailHtml = `
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Kode Transaksi</p>
                    <p class="font-medium">${data.kode_transaksi}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Tanggal</p>
                    <p class="font-medium">${formattedDate}</p>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Total Bayar</p>
                    <p class="font-medium">Rp ${new Intl.NumberFormat('id-ID').format(data.total_bayar)}</p>
                </div>
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 mb-2">Detail Item</p>
                    <div class="space-y-2">
            `;

                data.details.forEach(detail => {
                    detailHtml += `
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium">${detail.produk.nama}</p>
                            <p class="text-sm text-gray-600">${detail.jumlah} x Rp ${new Intl.NumberFormat('id-ID').format(detail.harga_jual_per_pcs)}</p>
                        </div>
                        <p class="font-medium">Rp ${new Intl.NumberFormat('id-ID').format(detail.jumlah * detail.harga_jual_per_pcs)}</p>
                    </div>
                `;
                });

                detailHtml += `
                    </div>
                </div>
            `;

                $('#detail-content').html(detailHtml);
                $('#detail-modal').removeClass('hidden').addClass('flex');
            });
        }

        // Close detail modal
        function closeDetailModal() {
            $('#detail-modal').removeClass('flex').addClass('hidden');
        }

        // Close modal when clicking outside
        $('#detail-modal').click(function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });
    </script>
@endpush
