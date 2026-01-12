<?php

namespace App\Filament\Resources\Deponses\Pages;

use App\Filament\Resources\Deponses\DeponseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeponse extends CreateRecord
{
    protected static string $resource = DeponseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Dépense créée avec succès';
    }
}
