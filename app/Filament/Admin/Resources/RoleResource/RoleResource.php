<?php

namespace App\Filament\Admin\Resources\RoleResource;

use App\Filament\Admin\Resources\RoleResource\Pages;
use App\Filament\Admin\Resources\RoleResource\Schemas\RoleForm;
use App\Filament\Admin\Resources\RoleResource\Tables\RoleTable;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return false;
        // return auth()->user()?->hasPermissionTo('manage_roles') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return RoleTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}