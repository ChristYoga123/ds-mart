<?php

namespace App\Filament\Admin\Resources\TransaksiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Admin\Resources\TransaksiResource;
use App\Filament\Admin\Resources\TransaksiResource\Widgets\TransaksiStatsOverview;

class ManageTransaksis extends ManageRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = TransaksiResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TransaksiStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
