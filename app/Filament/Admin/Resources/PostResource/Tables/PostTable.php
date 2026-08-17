<?php

namespace App\Filament\Admin\Resources\PostResource\Tables;

use App\Models\City;
use Filament\Actions;
use Filament\Forms;
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
                Tables\Columns\ImageColumn::make('images')
                    ->label('Images')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->getStateUsing(function ($record) {
                        if (!$record->news) {
                            return [];
                        }
                        
                        return $record->news->media()
                            ->whereHas('mediaType', fn($q) => $q->where('type_name', 'image'))
                            ->get()
                            ->map(fn($media) => $media->full_url)
                            ->toArray();
                    }),
                Tables\Columns\TextColumn::make('by_admin')
                    ->label('Created by Admin')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                Tables\Columns\TextColumn::make('news.body')
                    ->label('Body')
                    ->searchable()
                    ->limit(50),
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
                Tables\Filters\SelectFilter::make('city')
                    ->label('City')
                    ->options(City::all()->pluck('name', 'id'))
                    ->query(function ($query, $state) {
                        if ($state['value']) {
                            $query->whereHas('news.address.city', function ($q) use ($state) {
                                $q->where('id', $state['value']);
                            });
                        }
                    })
                    ->searchable(),

                Tables\Filters\Filter::make('by_admin')
                    ->label('Created by Admin')
                    ->query(fn ($query) => $query->where('by_admin', true)),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
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
