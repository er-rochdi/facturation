<?php

namespace App\Filament\Resources\Deponses\Tables;

use App\Models\Deponse;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class DeponsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                BadgeColumn::make('category')
                    ->label('Catégorie')
                    ->formatStateUsing(fn($state) => Deponse::categories()[$state] ?? $state)
                    ->colors([
                        'primary' => 'salaires',
                        'warning' => 'loyer',
                        'danger' => 'electricite',
                        'info' => 'eau',
                        'success' => 'internet',
                        'gray' => fn($state) => !in_array($state, ['salaires', 'loyer', 'electricite', 'eau', 'internet']),
                    ])
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->description),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('DHs')
                    ->sortable()
                    ->color('danger')
                    ->weight('bold'),

                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                BadgeColumn::make('payment_method')
                    ->label('Paiement')
                    ->formatStateUsing(fn($state) => Deponse::paymentMethods()[$state] ?? $state)
                    ->colors([
                        'success' => 'especes',
                        'info' => 'virement',
                        'warning' => 'cheque',
                        'primary' => 'carte',
                    ]),

                TextColumn::make('reference')
                    ->label('Réf.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Supprimé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(Deponse::categories())
                    ->multiple(),

                SelectFilter::make('payment_method')
                    ->label('Mode de paiement')
                    ->options(Deponse::paymentMethods()),

                SelectFilter::make('company_id')
                    ->label('Entreprise')
                    ->relationship('company', 'name'),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Du'),
                        DatePicker::make('date_to')
                            ->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
