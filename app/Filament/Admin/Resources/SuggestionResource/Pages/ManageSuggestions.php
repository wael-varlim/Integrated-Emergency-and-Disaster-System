<?php

namespace App\Filament\Admin\Resources\SuggestionResource\Pages;

use App\Filament\Admin\Resources\SuggestionResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSuggestions extends ManageRecords
{
    protected static string $resource = SuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [];  // Suggestions come from users, not created by admin
    }
}