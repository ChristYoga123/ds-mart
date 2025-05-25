<?php

namespace App\Filament\Admin\Widgets;

use Filament\Tables;
use App\Models\Transaksi;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Widgets\TableWidget as BaseWidget;

class TransaksiTableOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(Transaksi::query()->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('kode_transaksi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->money('idr')
                    ->weight(FontWeight::Bold)
                    ->getStateUsing(fn(Transaksi $transaksi) => $transaksi->details->sum(fn($detail) => $detail->harga_jual_per_pcs * $detail->jumlah)),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->money('idr')
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
