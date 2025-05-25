<?php

namespace App\Exports\Admin;

use App\Models\Transaksi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransaksiExport implements FromCollection, ShouldQueue
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $transaksi = Transaksi::query()->orderBy('created_at', 'desc')->get()->map(function ($item) {
            return [
                'tanggal' => $item->created_at->locale('id')->isoFormat('LL'),
                'kode_transaksi' => $item->kode_transaksi,
                'total_harga' => 'Rp ' . number_format($item->details->sum(fn($detail) => $detail->harga_jual_per_pcs * $detail->jumlah), 0, ',', '.'),
                'total_bayar' => 'Rp ' . number_format($item->total_bayar, 0, ',', '.'),
                'detail' => $item->details->map(function ($detail) {
                    return $detail->produk->nama . ' - ' . $detail->jumlah . 'pcs - ' . $detail->harga_jual_per_pcs . ' - ' . $detail->harga_jual_per_pcs * $detail->jumlah;
                }),
            ];
        })->collect();

        return $transaksi;
    }
}
