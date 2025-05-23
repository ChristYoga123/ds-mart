<?php

namespace App\Filament\Gudang\Resources\ProdukResource\Pages;

use Filament\Tables\Table;
use App\Models\ProdukBatch;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Gudang\Resources\ProdukResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ProdukBadgePage extends Page implements HasTable
{
    use InteractsWithTable, InteractsWithRecord;

    protected static string $resource = ProdukResource::class;

    protected static string $view = 'filament.gudang.resources.produk-resource.pages.produk-badge-page';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Batch Produk ' . $this->record->nama;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProdukBatch::query()->whereProdukId($this->record->id)->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('kode_batch'),
                TextColumn::make('harga_beli_per_pcs')
                    ->money('idr')
                    ->weight(FontWeight::Bold),
                TextColumn::make('stok_pcs_tersedia')
                    ->badge()
                    ->getStateUsing(fn(ProdukBatch $record) => $record->stok_pcs_tersedia > 0 ? $record->stok_pcs_tersedia . ' pcs' : 'Habis')
                    ->color(fn(ProdukBatch $record) => $record->stok_pcs_tersedia > 0 ? 'success' : 'danger'),
            ]);
    }
}
