<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class Dashboard extends PagesDashboard
{

    use HasFiltersAction;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament-panels::pages.dashboard';

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    DatePicker::make('startDate')
                        ->label('Tanggal Awal')
                        ->default(now()->startOfMonth()),
                    DatePicker::make('endDate')
                        ->label('Tanggal Akhir')
                        ->default(now()->endOfMonth()),
                    // ...
                ]),
        ];
    }
}
