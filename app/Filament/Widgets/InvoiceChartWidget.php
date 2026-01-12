<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class InvoiceChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public function getHeading(): string
    {
        return 'Factures par Statut';
    }

    public function getDescription(): string
    {
        return 'Répartition des factures selon leur statut';
    }

    protected function getData(): array
    {
        $year = $this->filters['year'] ?? now()->year;
        $clientId = $this->filters['client_id'] ?? null;
        $companyId = $this->filters['company_id'] ?? null;

        $query = Invoice::query()->whereYear('invoice_date', $year);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $paidCount = (clone $query)->where('status', 'paid')->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $overdueCount = (clone $query)->where('status', 'overdue')->count();
        $cancelledCount = (clone $query)->where('status', 'cancelled')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Factures',
                    'data' => [$paidCount, $pendingCount, $overdueCount, $cancelledCount],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.95)',   // Emerald - Paid
                        'rgba(251, 191, 36, 0.95)',  // Amber - Pending
                        'rgba(239, 68, 68, 0.95)',   // Red - Overdue
                        'rgba(148, 163, 184, 0.95)', // Slate - Cancelled
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 4,
                    'hoverOffset' => 15,
                    'hoverBorderWidth' => 0,
                ],
            ],
            'labels' => ['✅ Payées', '⏳ En attente', '⚠️ En retard', '❌ Annulées'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
            ],
            'cutout' => '60%',
            'animation' => [
                'animateScale' => true,
                'animateRotate' => true,
            ],
        ];
    }
}
