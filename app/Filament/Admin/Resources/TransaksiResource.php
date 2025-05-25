<?php

namespace App\Filament\Admin\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Transaksi;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Admin\TransaksiExport;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\TransaksiExporter;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Admin\Resources\TransaksiResource\Pages;
use App\Filament\Admin\Resources\TransaksiResource\RelationManagers;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use App\Filament\Admin\Resources\TransaksiResource\Widgets\TransaksiStatsOverview;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
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
                Forms\Components\TextInput::make('kode_transaksi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_bayar')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->action(fn() => Excel::download(new TransaksiExport(), 'transaksi' . now()->format('Y-m-d') . '.xlsx'))
                    ->color('success')
            ])
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
            ])
            ->filters([
                DateRangeFilter::make('created_at')
                    ->timezone('Asia/Jakarta')
                    ->label('Tanggal Transaksi')
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Transaksi $transaksi) => Pages\TransaksiDetailPage::getUrl(['record' => $transaksi]))
                    ->color('info'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            TransaksiStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransaksis::route('/'),
            'detail' => Pages\TransaksiDetailPage::route('/detail/{record}')
        ];
    }
}
