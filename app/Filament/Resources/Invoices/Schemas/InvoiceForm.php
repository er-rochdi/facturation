<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Invoice Details')
                ->schema([
                        Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required(),
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->label('Entreprise')
                            ->required(),
                        DatePicker::make('invoice_date')
                            ->required(),
                        TextInput::make('amount')
                            ->required()
                            ->numeric(),
                        DatePicker::make('due_date'),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'paid' => 'Paid',
                                'partial' => 'Partial',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                TextInput::make('designation')->required(),
                                TextInput::make('days')->numeric()->default(1)->required()->label('Nbr de jours'),
                                TextInput::make('unit_price')->numeric()->required()->label('Prix unitaire'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                        TextInput::make('pdf_path'),
                ])->columns(2)
                ->collapsible()
        ])->columns(1);

    }
}
