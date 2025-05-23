<?php

namespace App\Filament\Gudang\Resources\ProdukMutasiResource\Pages;

use App\Filament\Gudang\Resources\ProdukMutasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProdukMutasis extends ManageRecords
{
    protected static string $resource = ProdukMutasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
