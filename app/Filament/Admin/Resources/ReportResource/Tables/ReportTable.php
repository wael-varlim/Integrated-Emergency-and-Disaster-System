<?php

namespace App\Filament\Admin\Resources\ReportResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class ReportTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('news.body')
                    ->label('News')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->news?->body),

                Tables\Columns\TextColumn::make('news.address.street')
                    ->label('Address')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('news.user.knownUser.first_name')
                    ->label('Reported By')
                    ->formatStateUsing(fn ($record) =>
                        $record->news?->user?->knownUser
                            ? $record->news->user->knownUser->first_name . ' ' . $record->news->user->knownUser->last_name
                            : 'Anonymous'
                    ),

                Tables\Columns\IconColumn::make('news.post')
                    ->label('Has Post')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn ($record) => $record->news?->post !== null),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Reported At'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('has_post')
                    ->label('Has Post')
                    ->query(fn ($query) => $query->whereHas('news.post')),

                Tables\Filters\Filter::make('no_post')
                    ->label('No Post Yet')
                    ->query(fn ($query) => $query->whereDoesntHave('news.post')),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_report')),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_report')),
                ]),
            ]);
    }
}
