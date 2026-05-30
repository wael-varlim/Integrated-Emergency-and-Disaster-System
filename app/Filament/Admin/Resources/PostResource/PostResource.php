<?php

namespace App\Filament\Admin\Resources\PostResource;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\Schemas\PostForm;
use App\Filament\Admin\Resources\PostResource\Tables\PostTable;
use App\Models\Post;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;


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
        return $schema
            ->schema([
                Section::make('Post Details')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('owner_role'),
                    ]),
                Section::make('Information')
                    ->schema([
                        TextEntry::make('news.body')->label('News Body'),
                        TextEntry::make('news.address.city.name')->label('City'),
                        TextEntry::make('news.address.street')->label('Street'),
                    ]),
                Section::make('Notification Details')
                    ->schema([
                        TextEntry::make('notification.title')->label('Notification Title'),
                        TextEntry::make('notification.body')->label('Notification Body'),
                        TextEntry::make('notification.region.city.name')->label('Target Region'),
                    ])
                    ->visible(fn ($record) => $record->notification()->exists()),
            ]);
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
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}