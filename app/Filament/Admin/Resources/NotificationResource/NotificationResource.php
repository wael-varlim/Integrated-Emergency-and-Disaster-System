<?php

namespace App\Filament\Admin\Resources\NotificationResource;

use App\Filament\Admin\Resources\NotificationResource\Pages;
use App\Filament\Admin\Resources\NotificationResource\Schemas\NotificationForm;
use App\Filament\Admin\Resources\NotificationResource\Tables\NotificationTable;
use App\Models\Notification;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_notification', 'create_notification',
            'update_notification', 'delete_notification',
        ]) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return NotificationForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNotifications::route('/'),
        ];
    }
}