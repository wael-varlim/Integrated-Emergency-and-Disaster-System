<?php

namespace App\Filament\Admin\Resources\AuthorityResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class AuthorityTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('authorityType.type_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('authorityType.authorityTranslation')
                    ->label('Arabic Name')
                    ->formatStateUsing(function ($record) {
                        return $record->authorityType
                            ?->authorityTranslation
                            ->where('languahe_code', 'ar')
                            ->first()
                            ?->translation ?? '—';
                    }),

                Tables\Columns\TextColumn::make('news_count')
                    ->counts('news')
                    ->badge()
                    ->color('info')
                    ->label('News'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_authority')),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_authority')),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_authority')),
                ]),
            ]);
    }
}
