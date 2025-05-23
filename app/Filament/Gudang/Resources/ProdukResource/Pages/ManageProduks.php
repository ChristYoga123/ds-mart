<?php

namespace App\Filament\Gudang\Resources\ProdukResource\Pages;

use App\Filament\Gudang\Resources\ProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProduks extends ManageRecords
{
    protected static string $resource = ProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->closeModalByClickingAway(false),
        ];
    }
}
