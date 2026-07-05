<?php

namespace App\Filament\Admin\Resources\PostResource;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\Schemas\PostForm;
use App\Filament\Admin\Resources\PostResource\Schemas\PostInfolist;
use App\Filament\Admin\Resources\PostResource\Tables\PostTable;
use App\Models\Post;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    public static function form(Schema $schema): Schema
    {
        return PostForm::schema($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PostInfolist::schema($schema);
    }

    public static function table(Table $table): Table
    {
        return PostTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_post');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_post');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_post');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_post');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_post');
    }
}