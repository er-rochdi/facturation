<?php

namespace App\Filament\Widgets;

use App\Models\Deponse;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeponseStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected function getHeading(): ?string
    {
        return 'Aperçu des Dépenses';
    }

    protected function getStats(): array
    {
        $year = $this->filters['year'] ?? now()->year;
        $companyId = $this->filters['company_id'] ?? null;

        // Base query avec filtres
        $deponseQuery = Deponse::query()->whereYear('date', $year);

        if ($companyId) {
            $deponseQuery->where('company_id', $companyId);
        }

        // Statistiques principales
        $totalDeponses = (clone $deponseQuery)->sum('amount');
        $deponseCount = (clone $deponseQuery)->count();

        // Dépenses par catégorie (top 3)
        $depensesParCategorie = (clone $deponseQuery)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->take(3)
            ->pluck('total', 'category');

        // Données mensuelles pour le graphique
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthQuery = Deponse::query()
                ->whereYear('date', $year)
                ->whereMonth('date', $i);

            if ($companyId) {
                $monthQuery->where('company_id', $companyId);
            }

            $monthlyData[] = (float) $monthQuery->sum('amount');
        }

        // Comparaison avec l'année précédente
        $lastYearTotal = Deponse::query()
            ->whereYear('date', $year - 1)
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->sum('amount');

        $variationPercentage = $lastYearTotal > 0
            ? round((($totalDeponses - $lastYearTotal) / $lastYearTotal) * 100, 1)
            : 0;

        $variationDescription = $variationPercentage >= 0
            ? "+{$variationPercentage}% vs " . ($year - 1)
            : "{$variationPercentage}% vs " . ($year - 1);

        // Dépenses du mois actuel
        $currentMonthDeponses = Deponse::query()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->sum('amount');

        // Moyenne mensuelle
        $avgMonthly = $deponseCount > 0 ? $totalDeponses / 12 : 0;

        $stats = [
            Stat::make('Total Dépenses ' . $year, number_format($totalDeponses, 2, ',', ' ') . ' DH')
                ->description($variationDescription)
                ->descriptionIcon($variationPercentage <= 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up')
                ->color($variationPercentage <= 0 ? 'success' : 'danger')
                ->chart($monthlyData),

            Stat::make('Dépenses du Mois', number_format($currentMonthDeponses, 2, ',', ' ') . ' DH')
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),

            Stat::make('Nombre de Dépenses', $deponseCount)
                ->description('Transactions en ' . $year)
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info')
                ->chart(array_map(fn() => rand(2, 10), range(1, 7))),

            Stat::make('Moyenne Mensuelle', number_format($avgMonthly, 2, ',', ' ') . ' DH')
                ->description('Moyenne par mois')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('gray'),
        ];

        // Ajouter les top catégories
        $categoryColors = [
            'salaires' => 'primary',
            'loyer' => 'warning',
            'electricite' => 'danger',
            'eau' => 'info',
            'internet' => 'success',
        ];

        $categoryIcons = [
            'salaires' => 'heroicon-o-users',
            'loyer' => 'heroicon-o-home',
            'electricite' => 'heroicon-o-bolt',
            'eau' => 'heroicon-o-beaker',
            'internet' => 'heroicon-o-wifi',
            'fournitures' => 'heroicon-o-clipboard',
            'transport' => 'heroicon-o-truck',
            'maintenance' => 'heroicon-o-wrench',
            'marketing' => 'heroicon-o-megaphone',
            'assurance' => 'heroicon-o-shield-check',
            'impots' => 'heroicon-o-building-library',
            'banque' => 'heroicon-o-building-office',
            'autres' => 'heroicon-o-ellipsis-horizontal',
        ];

        foreach ($depensesParCategorie as $category => $total) {
            $categoryLabel = Deponse::categories()[$category] ?? ucfirst($category);
            $stats[] = Stat::make($categoryLabel, number_format($total, 2, ',', ' ') . ' DH')
                ->description('Catégorie majeure')
                ->descriptionIcon($categoryIcons[$category] ?? 'heroicon-o-tag')
                ->color($categoryColors[$category] ?? 'gray');
        }

        return $stats;
    }
}
