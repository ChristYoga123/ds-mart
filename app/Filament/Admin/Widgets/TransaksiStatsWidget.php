<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TransaksiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Hitung pendapatan hari ini
        $pendapatanHariIni = Transaksi::whereDate('created_at', $today)
            ->sum('total_bayar');

        // Hitung pendapatan bulan ini
        $pendapatanBulanIni = Transaksi::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_bayar');

        // Hitung laba (selisih antara harga jual dan harga beli)
        $laba = Transaksi::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->sum(function ($transaksi) {
                return $transaksi->details->sum(function ($detail) {
                    $hargaJual = $detail->harga_jual_per_pcs * $detail->jumlah;
                    $hargaBeli = $detail->produkBatch->harga_beli_per_pcs * $detail->jumlah;
                    return $hargaJual - $hargaBeli;
                });
            });

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Total pendapatan hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description('Total pendapatan bulan ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Laba Bulan Ini', 'Rp ' . number_format($laba, 0, ',', '.'))
                ->description('Total laba bulan ini')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
