<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $year = $this->filters['year'] ?? now()->year;
        $clientId = $this->filters['client_id'] ?? null;
        $companyId = $this->filters['company_id'] ?? null;

        // Base query avec filtres
        $invoiceQuery = Invoice::query()->whereYear('invoice_date', $year);

        if ($clientId) {
            $invoiceQuery->where('client_id', $clientId);
        }

        if ($companyId) {
            $invoiceQuery->where('company_id', $companyId);
        }

        // Statistiques
        $totalAmount = (clone $invoiceQuery)->sum('amount');
        $invoiceCount = (clone $invoiceQuery)->count();
        $paidAmount = (clone $invoiceQuery)->where('status', 'paid')->sum('amount');
        $pendingAmount = (clone $invoiceQuery)->where('status', 'pending')->sum('amount');

        // Données pour le graphique de tendance (12 derniers mois)
        $trendData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthQuery = Invoice::query()
                ->whereYear('invoice_date', $year)
                ->whereMonth('invoice_date', $i);

            if ($clientId) {
                $monthQuery->where('client_id', $clientId);
            }
            if ($companyId) {
                $monthQuery->where('company_id', $companyId);
            }

            $trendData[] = $monthQuery->sum('amount');
        }

        // Calcul de la croissance
        $lastYearTotal = Invoice::query()
            ->whereYear('invoice_date', $year - 1)
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->sum('amount');

        $growthPercentage = $lastYearTotal > 0
            ? round((($totalAmount - $lastYearTotal) / $lastYearTotal) * 100, 1)
            : 0;

        $growthDescription = $growthPercentage >= 0
            ? "+{$growthPercentage}% vs " . ($year - 1)
            : "{$growthPercentage}% vs " . ($year - 1);

        return [
            Stat::make('Total Clients', Client::count())
                ->description('Clients actifs enregistrés')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info')
                ->chart([3, 5, 7, 4, 9, 6, 8]),

            Stat::make('Chiffre d\'Affaires ' . $year, number_format($totalAmount, 2, ',', ' ') . ' DH')
                ->description($growthDescription)
                ->descriptionIcon($growthPercentage >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($growthPercentage >= 0 ? 'success' : 'danger')
                ->chart($trendData),

            Stat::make('Factures', $invoiceCount)
                ->description('Nombre de factures en ' . $year)
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->chart(array_map(fn() => rand(2, 15), range(1, 7))),

            Stat::make('Montant Payé', number_format($paidAmount, 2, ',', ' ') . ' DH')
                ->description('Paiements reçus')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('En Attente', number_format($pendingAmount, 2, ',', ' ') . ' DH')
                ->description('Paiements en suspens')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Entreprises', Company::count())
                ->description('Entreprises enregistrées')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('gray'),
        ];
    }
}
