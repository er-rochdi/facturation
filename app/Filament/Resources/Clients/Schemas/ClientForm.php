<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->schema([
                Section::make('client Details')
                    ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('address'),
                ])->columns(2)
                    ->collapsible()
            ])->columns(1);
    }
}
