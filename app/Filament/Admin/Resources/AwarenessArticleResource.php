<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AwarenessArticleResource\Pages;
use App\Models\AwarenessArticle;
use App\Models\NewsType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AwarenessArticleResource extends Resource
{
    protected static ?string $model = AwarenessArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_awareness_article', 'create_awareness_article',
            'update_awareness_article', 'delete_awareness_article',
        ]) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Article Details')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_awareness_article')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_awareness_article')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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