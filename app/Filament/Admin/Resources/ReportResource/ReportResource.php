<?php

namespace App\Filament\Admin\Resources\ReportResource;

use App\Filament\Admin\Resources\ReportResource\Pages;
use App\Filament\Admin\Resources\ReportResource\Schemas\ReportForm;
use App\Filament\Admin\Resources\ReportResource\Schemas\ReportInfolist;
use App\Filament\Admin\Resources\ReportResource\Tables\ReportTable;
use App\Models\Report;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Reports';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_report',
            'delete_report',
        ]) ?? false;
    }

    public static function canView($record): bool
    {
        return auth()->user()?->can('view_report') ?? false;
    }

    public static function canEdit($record): bool
    {
        return false; // Reports cannot be edited
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete_report') ?? false;
    }

    public static function canCreate(): bool
    {
        return false; // Reports are created by users via mobile app
    }

    public static function form(Schema $schema): Schema
    {
        return ReportForm::schema($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReportTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}
