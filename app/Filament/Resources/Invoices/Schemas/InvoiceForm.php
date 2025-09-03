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
                        TextInput::make('pdf_path'),
                ])->columns(2)
                ->collapsible()
        ])->columns(1);

    }
}
