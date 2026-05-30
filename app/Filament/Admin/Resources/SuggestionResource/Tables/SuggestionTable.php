<?php

namespace App\Filament\Admin\Resources\SuggestionResource\Tables;

use App\Models\Suggestion;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class SuggestionTable
{
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
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Suggestion $record) => ! $record->is_read_by_admin)
                    ->action(fn (Suggestion $record) => $record->update(['is_read_by_admin' => true])),

                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_suggestion')),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_suggestion')),
                ]),
            ]);
    }
}
