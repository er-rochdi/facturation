<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Invoice;
use Filament\Forms\Components\Select;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class QuarterlyStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $year = $this->filters['year'] ?? now()->year;
        $clientId = $this->filters['client_id'] ?? null;
        $company_id = $this->filters['company_id'] ?? null;

        $query = Invoice::query();

        // Filter by year
        if ($year) {
            $query->whereYear('invoice_date', $year);
        }

        // Filter by client
        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        // Filter by company
        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        // Calculate quarterly totals
        $q1Total = (clone $query)->whereMonth('invoice_date', '>=', 1)
            ->whereMonth('invoice_date', '<=', 3)
            ->sum('amount');

        $q2Total = (clone $query)->whereMonth('invoice_date', '>=', 4)
            ->whereMonth('invoice_date', '<=', 6)
            ->sum('amount');

        $q3Total = (clone $query)->whereMonth('invoice_date', '>=', 7)
            ->whereMonth('invoice_date', '<=', 9)
            ->sum('amount');

        $q4Total = (clone $query)->whereMonth('invoice_date', '>=', 10)
            ->whereMonth('invoice_date', '<=', 12)
            ->sum('amount');

        $yearTotal = $q1Total + $q2Total + $q3Total + $q4Total;

        return [
            Stat::make('T1 (Jan-Mar)', number_format($q1Total, 2) . ' DH')
                ->description('Trimestre 1')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('T2 (Avr-Jun)', number_format($q2Total, 2) . ' DH')
                ->description('Trimestre 2')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
            Stat::make('T3 (Jul-Sep)', number_format($q3Total, 2) . ' DH')
                ->description('Trimestre 3')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
            Stat::make('T4 (Oct-Déc)', number_format($q4Total, 2) . ' DH')
                ->description('Trimestre 4')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('danger'),
            Stat::make('Total Année ' . $year, number_format($yearTotal, 2) . ' DH')
                ->description('Total annuel')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
