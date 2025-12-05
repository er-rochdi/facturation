<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money('DHs')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'sent',
                        'success' => 'paid',
                        'info' => 'partial',
                        'danger' => 'overdue',
                        'gray' => 'cancelled',
                    ]),
                TextColumn::make('pdf_path')
                    ->label('PDF')
                    ->formatStateUsing(fn ($state) => $state ? '✓ Généré' : '✗ Non généré')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
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
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft' => 'Brouillon',
                        'sent' => 'Envoyé',
                        'paid' => 'Payé',
                        'partial' => 'Partiel',
                        'overdue' => 'En retard',
                        'cancelled' => 'Annulé',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                Action::make('generatePdf')
                    ->label('Générer PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        try {
                            // Générer le nom du fichier PDF
                            $filename = 'invoice_' . $record->id . '_' . now()->format('Y-m-d') . '.pdf';

                            // Créer le PDF avec les données de la facture
                            $pdf = Pdf::loadView('invoices.pdf', [
                                'invoice' => $record,
                                'client' => $record->client,
                            ]);

                            // Sauvegarder le PDF
                            $pdfContent = $pdf->output();
                            Storage::disk('public')->put('invoices/' . $filename, $pdfContent);

                            // Mettre à jour le chemin du PDF dans la base de données
                            $record->update([
                                'pdf_path' => 'invoices/' . $filename
                            ]);
                            Notification::make()
                                ->title('PDF généré avec succès')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Erreur lors de la génération du PDF')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn ($record) => $record->status !== 'cancelled'),

                Action::make('downloadPdf')
                    ->label('Télécharger PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn ($record) => $record->pdf_path ? Storage::url($record->pdf_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->pdf_path) && Storage::disk('public')->exists($record->pdf_path)),

                Action::make('viewPdf')
                    ->label('Voir Facture')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    \Filament\Actions\BulkAction::make('generatePdfs')
                        ->label('Générer PDFs')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('success')
                        ->action(function ($records) {
                            $successCount = 0;
                            $errorCount = 0;

                            foreach ($records as $record) {
                                try {
                                    $filename = 'invoice_' . $record->id . '_' . now()->format('Y-m-d') . '.pdf';

                                    $pdf = Pdf::loadView('invoices.pdf', [
                                        'invoice' => $record,
                                        'client' => $record->client,
                                    ]);

                                    $pdfContent = $pdf->output();
                                    Storage::disk('public')->put('invoices/' . $filename, $pdfContent);

                                    $record->update([
                                        'pdf_path' => 'invoices/' . $filename
                                    ]);

                                    $successCount++;
                                } catch (\Exception $e) {
                                    $errorCount++;
                                }
                            }

                            if ($successCount > 0) {
                                Notification::make()
                                    ->title("$successCount PDF(s) généré(s) avec succès")
                                    ->success()
                                    ->send();
                            }

                            if ($errorCount > 0) {
                                Notification::make()
                                    ->title("$errorCount erreur(s) lors de la génération")
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}