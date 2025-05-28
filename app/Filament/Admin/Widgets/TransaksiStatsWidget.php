<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class TransaksiStatsWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? Carbon::now()->startOfMonth();
        $endDate = $this->filters['endDate'] ?? Carbon::now()->endOfMonth();

        // Hitung pendapatan hari ini
        $pendapatanHariIni = Transaksi::whereDate('created_at', Carbon::today())
            ->sum('total_bayar');

        // Hitung pendapatan periode
        $pendapatanPeriode = Transaksi::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->sum('total_bayar');

        // Hitung laba periode
        $laba = Transaksi::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->get()
            ->sum(function ($transaksi) {
                return $transaksi->details->sum(function ($detail) {
                    $hargaJual = $detail->harga_jual_per_pcs * $detail->jumlah;
                    $hargaBeli = $detail->produkBatch->harga_beli_per_pcs * $detail->jumlah;
                    return $hargaJual - $hargaBeli;
                });
            });

        // Hitung pengeluaran kulakan periode
        $pengeluaranKulakan = Transaksi::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->get()
            ->sum(function ($transaksi) {
                return $transaksi->details->sum(function ($detail) {
                    return $detail->produkBatch->harga_beli_per_pcs * $detail->jumlah;
                });
            });

        // Hitung tabungan (10% dari omset kotor)
        $tabungan = $pendapatanPeriode * 0.1;

        // Hitung laba bersih
        $labaBersih = $laba - $tabungan;

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Total pendapatan hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pendapatan Periode', 'Rp ' . number_format($pendapatanPeriode, 0, ',', '.'))
                ->description('Total pendapatan periode terpilih')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('Laba Periode', 'Rp ' . number_format($laba, 0, ',', '.'))
                ->description('Total laba periode terpilih')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pengeluaran Kulakan', 'Rp ' . number_format($pengeluaranKulakan, 0, ',', '.'))
                ->description('Total pengeluaran untuk kulakan periode terpilih')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Tabungan (10%)', 'Rp ' . number_format($tabungan, 0, ',', '.'))
                ->description('10% dari omset kotor')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('Laba Bersih', 'Rp ' . number_format($labaBersih, 0, ',', '.'))
                ->description('Laba setelah dikurangi tabungan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
