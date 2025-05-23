<?php

namespace App\Filament\Admin\Resources\TransaksiResource\Pages;

use App\Filament\Admin\Resources\TransaksiResource;
use Filament\Resources\Pages\Page;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class TransaksiDetailPage extends Page implements HasTable
{
    use InteractsWithRecord, InteractsWithTable;
    protected static string $resource = TransaksiResource::class;
    protected static string $view = 'filament.admin.resources.transaksi-resource.pages.transaksi-detail-page';

    public function mount(int | string $record)
    {
        $this->record = $this->resolveRecord($record);
    }

    public Transaksi $transaksi;

    public function getTitle(): string|Htmlable
    {
        return 'Transaksi Detail Kode - ' . $this->record->kode_transaksi;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TransaksiDetail::query()->whereTransaksiId($this->record->id))
            ->columns([
                TextColumn::make('produk.nama')
                    ->searchable(),
                TextColumn::make('harga_jual_per_pcs')
                    ->label('Harga Jual Saat Transaksi')
                    ->money('idr')
                    ->weight(FontWeight::Bold),
                TextColumn::make('harga_kulakan_per_pcs')
                    ->label('Harga Kulakan Saat Transaksi')
                    ->money('idr')
                    ->weight(FontWeight::Bold),
                TextColumn::make('jumlah'),
                TextColumn::make('total')
                    ->getStateUsing(fn(TransaksiDetail $transaksiDetail) => $transaksiDetail->produk->harga_jual_per_pcs * $transaksiDetail->jumlah)
                    ->money('idr')
                    ->weight(FontWeight::Bold)
                
            ]);
    }
} 