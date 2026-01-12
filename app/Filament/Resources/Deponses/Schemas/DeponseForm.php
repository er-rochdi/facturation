<?php

namespace App\Filament\Resources\Deponses\Schemas;

use App\Models\Deponse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DeponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations de la Dépense')
                ->description('Renseignez les détails de la dépense')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Select::make('company_id')
                        ->relationship('company', 'name')
                        ->label('Entreprise')
                        ->searchable()
                        ->preload()
                        ->prefixIcon('heroicon-o-building-office'),

                    Select::make('category')
                        ->label('Catégorie')
                        ->options(Deponse::categories())
                        ->required()
                        ->searchable()
                        ->prefixIcon('heroicon-o-tag'),

                    TextInput::make('description')
                        ->label('Description')
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-document-text'),

                    TextInput::make('amount')
                        ->label('Montant (DH)')
                        ->required()
                        ->numeric()
                        ->prefix('DH')
                        ->minValue(0),

                    DatePicker::make('date')
                        ->label('Date')
                        ->required()
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    Select::make('payment_method')
                        ->label('Mode de paiement')
                        ->options(Deponse::paymentMethods())
                        ->default('especes')
                        ->required()
                        ->prefixIcon('heroicon-o-credit-card'),

                    TextInput::make('reference')
                        ->label('Référence / N° Facture')
                        ->maxLength(100)
                        ->prefixIcon('heroicon-o-hashtag'),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2)
                ->collapsible(),
        ])->columns(1);
    }
}
