<?php

namespace App\Filament\Admin\Resources\UserLogResource\Pages;

use App\Filament\Admin\Resources\UserLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUserLogs extends ManageRecords
{
    protected static string $resource = UserLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
