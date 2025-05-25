<?php

namespace App\Filament\Admin\Resources\ProdukMutasiResource\Pages;

use App\Filament\Admin\Resources\ProdukMutasiResource;
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
