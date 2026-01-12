<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class MonthlyRevenueChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        return 'Revenus Mensuels';
    }

    public function getDescription(): string
    {
        return 'Évolution des revenus par mois';
    }

    protected function getData(): array
    {
        $year = $this->filters['year'] ?? now()->year;
        $clientId = $this->filters['client_id'] ?? null;
        $companyId = $this->filters['company_id'] ?? null;

        $months = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $query = Invoice::query()
                ->whereYear('invoice_date', $year)
                ->whereMonth('invoice_date', $month);

            if ($clientId) {
                $query->where('client_id', $clientId);
            }

            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $months[] = Carbon::create()->month($month)->translatedFormat('M');
            $data[] = round($query->sum('amount'), 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenus (DH)',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.85)',   // Indigo - Jan
                        'rgba(139, 92, 246, 0.85)',   // Violet - Feb
                        'rgba(168, 85, 247, 0.85)',   // Purple - Mar
                        'rgba(16, 185, 129, 0.85)',   // Emerald - Apr
                        'rgba(20, 184, 166, 0.85)',   // Teal - May
                        'rgba(6, 182, 212, 0.85)',    // Cyan - Jun
                        'rgba(245, 158, 11, 0.85)',   // Amber - Jul
                        'rgba(249, 115, 22, 0.85)',   // Orange - Aug
                        'rgba(239, 68, 68, 0.85)',    // Red - Sep
                        'rgba(236, 72, 153, 0.85)',   // Pink - Oct
                        'rgba(244, 63, 94, 0.85)',    // Rose - Nov
                        'rgba(99, 102, 241, 0.85)',   // Indigo - Dec
                    ],
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                    'borderRadius' => 12,
                    'borderSkipped' => false,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => 'function(context) { return context.parsed.y.toLocaleString() + " DH"; }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return value.toLocaleString() + " DH"; }',
                    ],
                ],
            ],
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeInOutQuart',
            ],
        ];
    }
}
