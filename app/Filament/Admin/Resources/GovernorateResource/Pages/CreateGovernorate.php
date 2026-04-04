<?php

namespace App\Filament\Admin\Resources\GovernorateResource\Pages;

use App\Filament\Admin\Resources\GovernorateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGovernorate extends CreateRecord
{
    protected static string $resource = GovernorateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}