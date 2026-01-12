<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DeponseStatsWidget;
use App\Filament\Widgets\InvoiceChartWidget;
use App\Filament\Widgets\MonthlyRevenueChartWidget;
use App\Filament\Widgets\QuarterlyStatsWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopClientsWidget;
use App\Models\Client;
use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Tableau de Bord';

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
                    ->default($currentYear)
                    ->searchable()
                    ->prefixIcon('heroicon-o-calendar'),
                Select::make('client_id')
                    ->label('Client')
                    ->options(fn() => Client::orderBy('name')->pluck('name', 'id')->toArray())
                    ->placeholder('Tous les clients')
                    ->searchable()
                    ->prefixIcon('heroicon-o-user'),
                Select::make('company_id')
                    ->label('Entreprise')
                    ->options(fn() => Company::orderBy('name')->pluck('name', 'id')->toArray())
                    ->placeholder('Toutes les entreprises')
                    ->searchable()
                    ->prefixIcon('heroicon-o-building-office'),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            DeponseStatsWidget::class,
            QuarterlyStatsWidget::class,
            MonthlyRevenueChartWidget::class,
            InvoiceChartWidget::class,
            TopClientsWidget::class,
        ];
    }
}
