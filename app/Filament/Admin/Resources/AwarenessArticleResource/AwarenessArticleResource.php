<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource;

use App\Filament\Admin\Resources\AwarenessArticleResource\Pages;
use App\Filament\Admin\Resources\AwarenessArticleResource\Schemas\AwarenessArticleForm;
use App\Filament\Admin\Resources\AwarenessArticleResource\Tables\AwarenessArticleTable;
use App\Models\AwarenessArticle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class AwarenessArticleResource extends Resource
{
    protected static ?string $model = AwarenessArticle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_awareness_article', 'create_awareness_article',
            'update_awareness_article', 'delete_awareness_article',
        ]) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return AwarenessArticleForm::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return AwarenessArticleTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAwarenessArticles::route('/'),
        ];
    }
}