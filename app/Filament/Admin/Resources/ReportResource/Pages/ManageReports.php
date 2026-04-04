<?php

namespace App\Filament\Admin\Resources\ReportResource\Pages;

use App\Filament\Admin\Resources\ReportResource;
use Filament\Resources\Pages\ManageRecords;

class ManageReports extends ManageRecords
{
    protected static string $resource = ReportResource::class;

    // ✅ No create button - admin only browses and deletes
    protected function getHeaderActions(): array
    {
        return [];
    }
}