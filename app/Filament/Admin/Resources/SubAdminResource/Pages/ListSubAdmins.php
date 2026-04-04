<?php

namespace App\Filament\Admin\Resources\SubAdminResource\Pages;

use App\Filament\Admin\Resources\SubAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubAdmins extends ListRecords
{
    protected static string $resource = SubAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Sub Admin'),
        ];
    }
}