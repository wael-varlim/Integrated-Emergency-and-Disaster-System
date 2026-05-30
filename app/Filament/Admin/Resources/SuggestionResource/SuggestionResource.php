<?php

namespace App\Filament\Admin\Resources\SuggestionResource;

use App\Filament\Admin\Resources\SuggestionResource\Pages;
use App\Filament\Admin\Resources\SuggestionResource\Schemas\SuggestionForm;
use App\Filament\Admin\Resources\SuggestionResource\Tables\SuggestionTable;
use App\Models\Suggestion;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_suggestion', 'update_suggestion', 'delete_suggestion','create_suggestion'
        ]) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return SuggestionForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return SuggestionTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuggestions::route('/'),
            'create' => Pages\CreateSuggestion::route('/create'),
        ];
    }
}