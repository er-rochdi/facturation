<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopClientsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 1;

    protected function getTableHeading(): string
    {
        return 'Top 5 Meilleurs Clients';
    }

    public function table(Table $table): Table
    {
        $year = $this->pageFilters['year'] ?? now()->year;
        $clientId = $this->pageFilters['client_id'] ?? null;
        $companyId = $this->pageFilters['company_id'] ?? null;

        return $table
            ->query(
                Client::query()
                    ->when($clientId, fn (Builder $query) => $query->whereKey($clientId))
                    ->withSum([
                        'invoices as total_amount' => function (Builder $query) use ($year, $companyId) {
                            $query->whereYear('invoice_date', $year);
                            if ($companyId) {
                                $query->where('company_id', $companyId);
                            }
                        }
                    ], 'amount')
                    ->withCount([
                        'invoices as invoices_count' => function (Builder $query) use ($year, $companyId) {
                            $query->whereYear('invoice_date', $year);
                            if ($companyId) {
                                $query->where('company_id', $companyId);
                            }
                        }
                    ])
                    ->having('total_amount', '>', 0)
                    ->orderByDesc('total_amount')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Client')
                    ->icon('heroicon-o-user')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->label('Total Facturé')
                    ->formatStateUsing(fn($state) => number_format($state ?? 0, 2, ',', ' ') . ' DH')
                    ->color('success')
                    ->badge(),
                TextColumn::make('invoices_count')
                    ->label('Factures')
                    ->suffix(' factures')
                    ->color('primary'),
            ])
            ->paginated(false)
            ->striped();
    }
}
