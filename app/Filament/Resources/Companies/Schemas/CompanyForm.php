<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations générales')
                ->schema([
                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'individual' => 'Particulier',
                            'company' => 'Entreprise'
                        ])
                        ->default('individual')
                        ->required()
                        ->live(),

                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('address')
                        ->label('Adresse')
                        ->rows(3)
                        ->columnSpanFull(),

                    TextInput::make('phone')
                        ->label('Téléphone')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255),

                    TextInput::make('website')
                        ->label('Site web')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://exemple.com'),

                    FileUpload::make('logo')
                        ->label('Logo')
                        ->disk('public')
                        ->directory('companies/logos'),
                ])
                ->columns(2),

            Section::make('Informations légales')
                ->schema([
                    TextInput::make('ice')
                        ->label('ICE (Identifiant Commun de l\'Entreprise)')
                        ->maxLength(50),

                    TextInput::make('if')
                        ->label('IF (Identifiant Fiscal)')
                        ->maxLength(50),


                    TextInput::make('rc')
                        ->label('RC (Registre de Commerce)')
                        ->maxLength(50),

                    TextInput::make('patente')
                        ->label('Patente')
                        ->maxLength(50)
                        ->visible(fn ($get) => $get('type') === 'company'),

                    TextInput::make('cnie')
                        ->label('CNIE (pour particuliers)')
                        ->maxLength(20)
                        ->visible(fn ($get) => $get('type') === 'individual'),
                ])
                ->columns(2)
                ->collapsible(),
        ]);

    }
}
