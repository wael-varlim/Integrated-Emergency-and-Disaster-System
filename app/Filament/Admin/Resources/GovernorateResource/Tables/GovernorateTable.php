<?php

namespace App\Filament\Admin\Resources\GovernorateResource\Tables;

use App\Models\Region;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class GovernorateTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('region_id')
                    ->label('Region ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('city_count')
                    ->counts('city')
                    ->label('Cities')
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
                    ->options(Region::all()->pluck('id', 'id')),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_governorate')),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_governorate')),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_governorate')),
                ]),
            ]);
    }
}
