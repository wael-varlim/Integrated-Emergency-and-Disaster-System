<?php

namespace App\Filament\Admin\Resources\PostResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class PostTable
{
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
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
