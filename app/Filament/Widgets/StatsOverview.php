<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\Invoice;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Clients', Client::count())
                ->description('Nombre total de clients')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total Montant', number_format(Invoice::sum('amount'), 2) . ' DH')
                ->description('Montant total des factures')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
