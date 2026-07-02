<?php

namespace App\Filament\Admin\Resources\GovernorateResource;

use App\Filament\Admin\Resources\GovernorateResource\Pages;
use App\Filament\Admin\Resources\GovernorateResource\Schemas\GovernorateForm;
use App\Filament\Admin\Resources\GovernorateResource\Tables\GovernorateTable;
use App\Models\Governorate;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|\UnitEnum|null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return GovernorateForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return GovernorateTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGovernorates::route('/'),
            'create' => Pages\CreateGovernorate::route('/create'),
            'edit'   => Pages\EditGovernorate::route('/{record}/edit'),
        ];
    }
}