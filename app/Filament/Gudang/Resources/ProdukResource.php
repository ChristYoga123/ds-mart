<?php

namespace App\Filament\Gudang\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Produk;
use App\Models\UserLog;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Gudang\Resources\ProdukResource\Pages;
use App\Filament\Gudang\Resources\ProdukResource\RelationManagers;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\Select::make('produk_golongan_id')
                            ->label('Golongan Produk (Jika opsi tidak ada, klik tombol + untuk membuat opsi baru)')
                            ->relationship('produkGolongan', 'nama')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                            ]),
                        Forms\Components\TextInput::make('barcode')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Produk')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('harga_jual_per_pcs')
                            ->required()
                            ->prefix('Rp')
                            ->suffix('.00')
                            ->numeric()
                            ->minValue(0),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Produk::query()->withSum('produkBatches', 'stok_pcs_tersedia')->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('produkGolongan.nama')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode (Opsional)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga_jual_per_pcs')
                    ->numeric()
                    ->money('idr')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok_pcs_tersedia')
                    ->badge()
                    ->getStateUsing(fn(Produk $record) => $record->produk_batches_sum_stok_pcs_tersedia > 0 ? $record->produk_batches_sum_stok_pcs_tersedia . ' pcs' : 'Habis')
                    ->color(fn(Produk $record) => $record->produk_batches_sum_stok_pcs_tersedia > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('lihatMutasi')
                    ->url(fn(Produk $record): string => ProdukResource::getUrl('mutasi', ['record' => $record->id]))
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->label('Lihat Mutasi'),
                Tables\Actions\Action::make('lihatBatch')
                    ->url(fn(Produk $record): string => ProdukResource::getUrl('badge', ['record' => $record->id]))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->label('Lihat Batch'),
                Tables\Actions\EditAction::make()
                    ->after(function (Produk $record) {
                        UserLog::create([
                            'user_id' => auth()->user()->id,
                            'log' => 'Mengubah produk: ' . $record->nama
                        ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Produk $record) {
                        UserLog::create([
                            'user_id' => auth()->user()->id,
                            'log' => 'Menghapus produk: ' . $record->nama
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProduks::route('/'),
            'badge' => Pages\ProdukBadgePage::route('/badge/{record}'),
            'mutasi' => Pages\ProdukMutasiPage::route('/mutasi/{record}'),
        ];
    }
}
