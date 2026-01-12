<?php

namespace App\Filament\Resources\Deponses\Pages;

use App\Filament\Resources\Deponses\DeponseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeponses extends ListRecords
{
    protected static string $resource = DeponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvelle Dépense')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
