<?php

namespace App\Filament\Admin\Resources\CityResource\Tables;

use App\Models\Governorate;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class CityTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('governorate.name')
                    ->label('Governorate')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('region_id')
                    ->label('Region ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('governorate_id')
                    ->label('Governorate')
                    ->options(Governorate::all()->pluck('name', 'id')),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_city')),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_city')),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_city')),
                ]),
            ]);
    }
}
