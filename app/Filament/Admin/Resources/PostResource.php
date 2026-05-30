<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use App\Models\Address;
use App\Models\City;
use App\Models\User;
use App\Models\Region;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Infolists\Components\TextEntry;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    public static function form(Schema $schema): Schema
{
    return $schema
        ->schema([
            Section::make('Post Details')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('owner_role')
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make('News Information')
                ->schema([
                    Textarea::make('news_body')
                        ->required()
                        ->label('News Body')
                        ->rows(5)
                        ->dehydrated(true),

                    Select::make('city_id')
                        ->label('City')
                        ->options(City::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->dehydrated(true),
                    TextInput::make('street')
                        ->label('Street')
                        ->required()
                        ->maxLength(255)
                        ->dehydrated(true),
                ]),

            Section::make('Optional Notification')
                ->schema([
                    Toggle::make('create_notification')
                        ->label('Create a notification for this post?')
                        ->live()
                        ->default(false)
                        ->dehydrated(true),
                    Group::make()
                        ->schema([
                            TextInput::make('notification_title')
                                ->label('Notification Title')
                                ->required()
                                ->dehydrated(true),
                            Textarea::make('notification_body')
                                ->label('Notification Body')
                                ->required()
                                ->rows(3)
                                ->dehydrated(true),
                            Select::make('region_id')
                                ->label('Target Region')
                                ->options(Region::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->dehydrated(true),
                        ])
                        ->visible(fn (Get $get) => $get('create_notification') === true),
                ]),
        ]);
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
                        TextEntry::make('notification.region.name')->label('Target Region'),
                    ])
                    ->visible(fn ($record) => $record->notification()->exists()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('news.body')
                ->label('body')
                ->searchable(),
                Tables\Columns\TextColumn::make('news.address.city.name')
                    ->label('City')
                    ->searchable(),
                Tables\Columns\TextColumn::make('news.address.street')
                    ->label('Street')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
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