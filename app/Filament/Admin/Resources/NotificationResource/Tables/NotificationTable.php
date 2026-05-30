<?php

namespace App\Filament\Admin\Resources\NotificationResource\Tables;

use App\Models\Region;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('body')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('region.city.name')
                    ->label('Region')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region_id')
                    ->label('Region')
                    ->options(Region::with('city')->get()->pluck('city.name', 'id')->filter()),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_notification')),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_notification')),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_notification')),
                ]),
            ]);
    }
}
