<?php

namespace App\Filament\Gudang\Resources\ProdukResource\Pages;

use Filament\Actions;
use App\Models\UserLog;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Gudang\Resources\ProdukResource;

class ManageProduks extends ManageRecords
{
    protected static string $resource = ProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->closeModalByClickingAway(false)
                ->after(function ($record) {
                    UserLog::create([
                        'user_id' => auth()->user()->id,
                        'log' => 'Membuat produk: ' . $record->nama
                    ]);
                }),
        ];
    }
}
