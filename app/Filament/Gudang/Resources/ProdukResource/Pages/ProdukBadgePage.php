<?php

namespace App\Filament\Gudang\Resources\ProdukResource\Pages;

use Closure;
use App\Models\Produk;
use Filament\Forms\Get;
use Filament\Tables\Table;
use App\Models\ProdukBatch;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
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
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        Hidden::make('produk_id')
                            ->default($this->record->id),
                        TextInput::make('kode_batch')
                                ->label('Kode Batch (Opsional, jika tidak diisi maka akan otomatis dibuatkan kode batch baru)'),
                        TextInput::make('harga_beli_per_pcs')
                                ->label('Harga Beli Per Pcs')
                                ->numeric()
                                ->minValue(0)
                                ->rule(static function(Get $get, Component $component) {
                                    return static function(string $attribute, $value, Closure $fail) use ($get, $component) {
                                        $hargaBeliPerPcs = $get('harga_beli_per_pcs');
                                        $produkHargaJual = Produk::find($get('produk_id'));

                                        if ($produkHargaJual) {
                                            if ($hargaBeliPerPcs >= $produkHargaJual->harga_jual_per_pcs) {
                                                $fail('Harga beli per pcs tidak boleh lebih besar dari harga jual per pcs produk ' . $produkHargaJual->nama . ' dengan harga jual per pcs Rp' . number_format($produkHargaJual->harga_jual_per_pcs, 0, ',', '.'));
                                            }
                                        }

                                    };
                                })
                                ->default(0)
                                ->prefix('Rp')
                                ->suffix('.00')
                                ->required(),
                    ])
                    ->visible(fn(ProdukBatch $record) => $record->stok_pcs_tersedia > 0),
            ]);
    }
}
