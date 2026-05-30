<?php

namespace App\Filament\Admin\Resources\SubAdminResource;

use App\Filament\Admin\Resources\SubAdminResource\Pages;
use App\Filament\Admin\Resources\SubAdminResource\Schemas\SubAdminForm;
use App\Filament\Admin\Resources\SubAdminResource\Tables\SubAdminTable;
use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubAdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Sub Admins';

    protected static ?string $modelLabel = 'Sub Admin';

    protected static ?string $pluralModelLabel = 'Sub Admins';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('manage_sub_admins') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_type', 'known')
            ->whereHas('roles')
            ->where('id', '!=', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return SubAdminForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return SubAdminTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubAdmins::route('/'),
            'create' => Pages\CreateSubAdmin::route('/create'),
            'edit'   => Pages\EditSubAdmin::route('/{record}/edit'),
        ];
    }
}