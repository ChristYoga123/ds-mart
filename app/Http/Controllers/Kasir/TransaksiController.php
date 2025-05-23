<?php

namespace App\Http\Controllers\Kasir;

use App\Models\Transaksi;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        return view('pages.kasir.transaksi.index');
    }

    public function data()
    {
        $data = Transaksi::with('details.produk')
            ->orderBy('created_at', 'desc');

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function widget()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalTransaksiHariIni = Transaksi::whereDate('created_at', $today)->count();
        $totalPendapatanHariIni = Transaksi::whereDate('created_at', $today)->sum('total_bayar');
        $totalTransaksiBulanIni = Transaksi::whereDate('created_at', '>=', $startOfMonth)->count();
        $totalPendapatanBulanIni = Transaksi::whereDate('created_at', '>=', $startOfMonth)->sum('total_bayar');

        return response()->json([
            'total_transaksi_hari_ini' => $totalTransaksiHariIni,
            'total_pendapatan_hari_ini' => $totalPendapatanHariIni,
            'total_transaksi_bulan_ini' => $totalTransaksiBulanIni,
            'total_pendapatan_bulan_ini' => $totalPendapatanBulanIni
        ]);
    }

    public function detail($kodeTransaksi)
    {
        $transaksi = Transaksi::with(['details.produk', 'details.produkBatch'])
            ->where('kode_transaksi', $kodeTransaksi)
            ->firstOrFail();

        return response()->json($transaksi);
    }
} 