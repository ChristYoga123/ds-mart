<?php

namespace App\Filament\Gudang\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Produk;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProdukMutasi;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Gudang\Resources\ProdukMutasiResource\Pages;
use App\Filament\Gudang\Resources\ProdukMutasiResource\RelationManagers;
use Filament\Tables\Enums\FiltersLayout;

class ProdukMutasiResource extends Resource
{
    protected static ?string $model = ProdukMutasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Mutasi Produk';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(ProdukMutasi::query()->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('produkBatch.produk.nama')
                    ->label('Produk'),
                Tables\Columns\TextColumn::make('produkBatch.kode_batch')
                    ->label('Kode Batch'),
                Tables\Columns\TextColumn::make('tanggal_mutasi')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('jenis_mutasi')
                    ->badge()
                    ->color(fn(ProdukMutasi $record) => $record->jenis_mutasi == 'masuk' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('jumlah_mutasi')
                    ->numeric()
                    ->suffix('pcs'),
                Tables\Columns\TextColumn::make('keterangan')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('produk')
                    ->form([
                        Select::make('produk_id')
                            ->label('Produk')
                            ->options(Produk::all()->pluck('nama', 'id'))
                            ->searchable()
                            ->preload()
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['produk_id'], function (Builder $query) use ($data) {
                            return $query->whereHas('produkBatch', function (Builder $query) use ($data) {
                                $query->where('produk_id', $data['produk_id']);
                            });
                        });
                    })
                ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageProdukMutasis::route('/'),
        ];
    }
}
