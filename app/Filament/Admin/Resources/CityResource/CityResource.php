<?php

namespace App\Filament\Admin\Resources\CityResource;

use App\Filament\Admin\Resources\CityResource\Pages;
use App\Filament\Admin\Resources\CityResource\Schemas\CityForm;
use App\Filament\Admin\Resources\CityResource\Tables\CityTable;
use App\Models\City;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_city', 'create_city', 'update_city', 'delete_city',
        ]) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return CityForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return CityTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit'   => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}