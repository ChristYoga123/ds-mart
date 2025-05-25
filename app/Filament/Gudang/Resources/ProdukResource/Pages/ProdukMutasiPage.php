<?php

namespace App\Filament\Gudang\Resources\ProdukResource\Pages;

use App\Models\UserLog;
use Filament\Tables\Table;
use App\Models\ProdukBatch;
use App\Models\ProdukMutasi;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Gudang\Resources\ProdukResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class ProdukMutasiPage extends Page implements HasTable
{
    use InteractsWithTable, InteractsWithRecord;

    protected static string $resource = ProdukResource::class;

    protected static string $view = 'filament.gudang.resources.produk-resource.pages.produk-mutasi-page';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string|Htmlable
    {
        return 'Mutasi Produk ' . $this->record->nama;
    }
    
    public function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(ProdukMutasi::class)
                ->form([
                    Select::make('produk_batch_id')
                        ->label('Kode Batch (Jika opsi tidak ada, klik tombol + untuk membuat batch baru)')
                        ->relationship('produkBatch', 'kode_batch', fn($query) => $query->whereProdukId($this->record->id))
                        ->getOptionLabelFromRecordUsing(fn(ProdukBatch $record) => "[$record->kode_batch] " . $record->produk->nama . ' - Rp' . number_format($record->harga_beli_per_pcs, 0, ',', '.'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Hidden::make('produk_id')
                                ->default($this->record->id),
                            TextInput::make('kode_batch')
                                ->label('Kode Batch (Opsional, jika tidak diisi maka akan otomatis dibuatkan kode batch baru)'),
                            TextInput::make('harga_beli_per_pcs')
                                ->label('Harga Beli Per Pcs')
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->prefix('Rp')
                                ->suffix('.00')
                                ->required(),
                        ]),
                    DatePicker::make('tanggal_mutasi')
                        ->label('Tanggal Mutasi')
                        ->default(now())
                        ->required(),
                    Select::make('jenis_mutasi')
                        ->label('Jenis Mutasi')
                        ->options([
                            'masuk' => 'Masuk',
                            'keluar' => 'Keluar',
                        ])
                        ->required(),
                    TextInput::make('jumlah_mutasi')
                        ->label('Jumlah Mutasi')
                        ->suffix('pcs')
                        ->required(),
                    Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->required(),
                ])
                ->after(function (ProdukMutasi $record) {
                    $record->produkBatch->update([
                        'stok_pcs_tersedia' => $record->jenis_mutasi == 'masuk' ? $record->produkBatch->stok_pcs_tersedia + $record->jumlah_mutasi : $record->produkBatch->stok_pcs_tersedia - $record->jumlah_mutasi
                    ]);

                    UserLog::create([
                        'user_id' => auth()->user()->id,
                        'log' => 'Membuat mutasi untuk produk ' . $record->produkBatch->produk->nama . ' dengan kode batch ' . $record->produkBatch->kode_batch . ' sebanyak ' . $record->jumlah_mutasi . ' pcs'
                    ]);
                })
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProdukMutasi::query()->whereHas('produkBatch', fn($query) => $query->whereProdukId($this->record->id))->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('produkBatch.kode_batch')
                    ->label('Kode Batch'),
                TextColumn::make('tanggal_mutasi')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('jenis_mutasi')
                    ->badge()
                    ->color(fn(ProdukMutasi $record) => $record->jenis_mutasi == 'masuk' ? 'success' : 'danger'),
                TextColumn::make('jumlah_mutasi')
                    ->suffix('pcs')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->wrap(),
            ]);
    }
    
}
