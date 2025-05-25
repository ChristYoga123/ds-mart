<?php

namespace App\Filament\Admin\Resources\TransaksiResource\Widgets;

use Carbon\Carbon;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Admin\Resources\TransaksiResource\Pages\ManageTransaksis;

class TransaksiStatsOverview extends BaseWidget
{
    use InteractsWithPageTable;
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    protected function getTablePage(): string
    {
        return ManageTransaksis::class;
    }
    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        // Pendapatan sesuai filter
        $pendapatan = $query->sum('total_bayar');

        // Laba sesuai filter
        $laba = $query->get()->sum(function ($transaksi) {
            return $transaksi->details->sum(function ($detail) {
                $hargaJual = $detail->harga_jual_per_pcs * $detail->jumlah;
                $hargaBeli = $detail->produkBatch->harga_beli_per_pcs * $detail->jumlah;
                return $hargaJual - $hargaBeli;
            });
        });

        return [
            Stat::make('Pendapatan', 'Rp ' . number_format($pendapatan, 0, ',', '.'))
                ->description('Total pendapatan sesuai filter')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Laba', 'Rp ' . number_format($laba, 0, ',', '.'))
                ->description('Total laba sesuai filter')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
