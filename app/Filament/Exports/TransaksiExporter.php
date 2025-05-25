<?php

namespace App\Filament\Exports;

use App\Models\Transaksi;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransaksiExporter extends Exporter
{
    protected static ?string $model = Transaksi::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')
                ->label('Tanggal Transaksi')
                ->state(function (Transaksi $record) {
                    return $record->created_at->locale('id')->isoFormat('LL');
                }),
            ExportColumn::make('kode_transaksi'),
            ExportColumn::make('total_harga')
                ->prefix('Rp')
                ->state(function (Transaksi $record) {
                    return number_format($record->details->sum(fn($detail) => $detail->harga_jual_per_pcs * $detail->jumlah), 0, ',', '.');
                }),
            ExportColumn::make('total_bayar')
                ->prefix('Rp')
                ->state(function (Transaksi $record) {
                    return number_format($record->total_bayar, 0, ',', '.');
                }),
            ExportColumn::make('detail')
                ->state(function (Transaksi $record) {
                    return $record->details->map(fn($detail) => $detail->produk->nama . ' - ' . $detail->jumlah . ' pcs')->implode(', ');
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaksi export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
