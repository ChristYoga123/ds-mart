<?php

namespace App\Filament\Admin\Pages\Custom;

use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Pages\Dashboard as PagesDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Contracts\Support\Htmlable;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class Dashboard extends PagesDashboard
{
    use HasFiltersForm;


    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DateRangeFilter::make('created_at')
                            ->label('Rentang Tanggal')
                    ])
                    ->columns(3),
            ]);
    }
}
