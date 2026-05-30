<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AwarenessArticleResource\Pages;
use App\Models\AwarenessArticle;
use App\Models\NewsType;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

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
        return $schema
            ->schema([
                SchemaComponents\Section::make('Article Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Title')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(6)
                            ->label('Body')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('news_type_id')
                            ->label('News Type')
                            ->options(NewsType::all()->pluck('type_name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\FileUpload::make('icon_url')
                            ->label('Icon')
                            ->image()
                            ->disk('public')
                            ->directory('awareness-icons')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon_url')
                    ->label('Icon')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('body')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('newsType.type_name')
                    ->label('News Type')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('news_type_id')
                    ->label('News Type')
                    ->options(NewsType::all()->pluck('type_name', 'id')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_awareness_article')),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_awareness_article')),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_awareness_article')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAwarenessArticles::route('/'),
        ];
    }
}