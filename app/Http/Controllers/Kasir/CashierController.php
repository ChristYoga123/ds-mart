<?php

namespace App\Http\Controllers\Kasir;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\ProdukMutasi;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Produk::withSum('produkBatches', 'stok_pcs_tersedia');
            return datatables()
                ->of($data)
                ->addColumn('stok', function($row)
                {
                    return $row->produk_batches_sum_stok_pcs_tersedia;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.kasir.cashier.index');
    }

    public function bayar(Request $request)
    {
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
