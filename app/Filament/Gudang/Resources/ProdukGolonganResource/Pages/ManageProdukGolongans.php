<?php

namespace App\Filament\Gudang\Resources\ProdukGolonganResource\Pages;

use App\Filament\Gudang\Resources\ProdukGolonganResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProdukGolongans extends ManageRecords
{
    protected static string $resource = ProdukGolonganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->closeModalByClickingAway(false),
        ];
    }

    public function getTitle(): string
    {
        return 'Golongan Produk';
    }
}
