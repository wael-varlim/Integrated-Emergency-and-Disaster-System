<?php

namespace App\Filament\Admin\Resources\AuthorityResource;

use App\Filament\Admin\Resources\AuthorityResource\Pages;
use App\Filament\Admin\Resources\AuthorityResource\Schemas\AuthorityForm;
use App\Filament\Admin\Resources\AuthorityResource\Tables\AuthorityTable;
use App\Models\Authority;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class AuthorityResource extends Resource
{
    protected static ?string $model = Authority::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Authorities';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_authority',
            'create_authority',
            'update_authority',
            'delete_authority',
        ]) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return AuthorityForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return AuthorityTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAuthorities::route('/'),
            'create' => Pages\CreateAuthority::route('/create'),
            'edit'   => Pages\EditAuthority::route('/{record}/edit'),
        ];
    }
}