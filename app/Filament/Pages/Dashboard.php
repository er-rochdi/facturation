<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\QuarterlyStatsWidget;
use App\Filament\Widgets\StatsOverview;
use App\Models\Client;
use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $form): Schema
    {
        $years = [];
        $currentYear = now()->year;
        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
            $years[$i] = (string) $i;
        }

        return $form
            ->schema([
                Select::make('year')
                    ->label('Année')
                    ->options($years)
                    ->default($currentYear),
                Select::make('client_id')
                    ->label('Client')
                    ->options(Client::pluck('name', 'id')->toArray())
                    ->placeholder('Tous les clients'),
                Select::make('company_id')
                    ->label('Entreprise')
                    ->options(Company::pluck('name', 'id')->toArray())
                    ->placeholder('Toutes les entreprises'),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            QuarterlyStatsWidget::class,
        ];
    }
}
