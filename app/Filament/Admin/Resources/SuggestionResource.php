<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SuggestionResource\Pages;
use App\Models\Suggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_suggestion', 'update_suggestion', 'delete_suggestion','create_suggestion'
        ]) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->rows(5)
                            ->label('Content')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_read_by_admin')
                            ->label('Mark as Read')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->limit(60)
                    ->searchable()
                    ->label('Content'),

                Tables\Columns\IconColumn::make('is_read_by_admin')
                    ->boolean()
                    ->label('Read'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Submitted'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read_by_admin')
                    ->label('Read Status')
                    ->trueLabel('Read')
                    ->falseLabel('Unread'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // ✅ Mark as read directly from table
                Tables\Actions\Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Suggestion $record) => ! $record->is_read_by_admin)
                    ->action(fn (Suggestion $record) => $record->update(['is_read_by_admin' => true])),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_suggestion')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_suggestion')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSuggestions::route('/'),
            'create' => Pages\CreateSuggestion::route('/create'),
        ];
    }
}