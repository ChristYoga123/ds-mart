<?php

namespace App\Http\Controllers\Kasir;

use App\Models\Produk;
use App\Models\UserLog;
use App\Models\Transaksi;
use App\Models\ProdukMutasi;
use Illuminate\Http\Request;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        if(!Auth::user()->hasRole('kasir'))
        {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        if ($request->ajax()) {
            $data = Produk::withSum('produkBatches', 'stok_pcs_tersedia');
            return datatables()
                ->of($data)
                ->addColumn('stok', function($row)
                {
                    return $row->produk_batches_sum_stok_pcs_tersedia ?? 0;
                })
                ->addColumn('aksi', function($row)
                {
                    if($row->produk_batches_sum_stok_pcs_tersedia > 0)
                    {
                        return '<button class="add-to-cart bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white p-2 rounded-lg transition-all duration-200 hover:scale-105 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                            </svg>
                        </button>';
                    }

                    return '<button class="bg-gray-400 text-white p-2 rounded-lg cursor-not-allowed opacity-50" disabled title="Stok Habis">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 15v-3h4v3H8z"/>
                        </svg>
                    </button>';
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.kasir.cashier.index');
    }

    public function bayar(Request $request)
    {
        if(!Auth::user()->hasRole('kasir'))
        {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        try {
            DB::beginTransaction();

            // Validasi request
            $request->validate([
                'cart' => 'required|array',
                'cart.*.id' => 'required|exists:produks,barcode',
                'cart.*.quantity' => 'required|integer|min:1',
                'total_bayar' => 'required|numeric|min:0'
            ]);

            // Generate kode transaksi
            $kodeTransaksi = 'TRX-' . now()->format('YmdHis');

            // Buat transaksi baru
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'total_bayar' => $request->total_bayar
            ]);

            // Proses setiap item di cart
            foreach ($request->cart as $item) {
                $produk = Produk::where('barcode', $item['id'])->first();
                $sisaQty = $item['quantity'];
                $totalHargaKulakan = 0;

                // Ambil batch berdasarkan FIFO (created_at ASC)
                $batches = $produk->produkBatches()
                    ->where('stok_pcs_tersedia', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Proses setiap batch untuk memenuhi quantity
                foreach ($batches as $batch) {
                    if ($sisaQty <= 0) break;

                    $qtyDiambil = min($sisaQty, $batch->stok_pcs_tersedia);
                    
                    // Buat transaksi detail
                    TransaksiDetail::create([
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $produk->id,
                        'produk_batch_id' => $batch->id,
                        'jumlah' => $qtyDiambil,
                        'harga_kulakan_per_pcs' => $batch->harga_beli_per_pcs,
                        'harga_jual_per_pcs' => $produk->harga_jual_per_pcs
                    ]);

                    // Update stok batch
                    $batch->update([
                        'stok_pcs_tersedia' => $batch->stok_pcs_tersedia - $qtyDiambil
                    ]);

                    // Buat mutasi produk
                    ProdukMutasi::create([
                        'produk_batch_id' => $batch->id,
                        'tanggal_mutasi' => now(),
                        'jenis_mutasi' => 'keluar',
                        'jumlah_mutasi' => $qtyDiambil,
                        'keterangan' => "Penjualan - {$kodeTransaksi} - {$produk->nama} (Batch: {$batch->kode_batch})"
                    ]);

                    $totalHargaKulakan += ($batch->harga_beli_per_pcs * $qtyDiambil);
                    $sisaQty -= $qtyDiambil;

                    UserLog::create([
                        'user_id' => auth()->user()->id,
                        'log' => 'Membuat penjualan untuk produk ' . $produk->nama . ' dengan kode batch ' . $batch->kode_batch . ' sebanyak ' . $qtyDiambil . ' pcs'
                    ]);
                }
                
                // Jika masih ada sisa quantity yang belum terpenuhi
                if ($sisaQty > 0) {
                    throw new \Exception("Stok {$produk->nama} tidak mencukupi");
                }
            }

            DB::commit();
            return response()->json([
                'success' => 'Transaksi berhasil disimpan',
                'kode_transaksi' => $kodeTransaksi
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
