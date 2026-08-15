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

                Tables\Columns\TextColumn::make('news.knownUser')
                    ->label('Reported By')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->news && $record->news->knownUser) {
                            return $record->news->knownUser->first_name . ' ' . $record->news->knownUser->last_name;
                        }
                        return 'Anonymous';
                    })
                    ->url(function ($record) {
                        if ($record->news && $record->news->knownUser && $record->news->knownUser->user_id) {
                            return route('filament.admin.resources.users.view', ['record' => $record->news->knownUser->user_id]);
                        }
                        return null;
                    })
                    ->color('primary')
                    ->searchable(['body']),

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
            ->recordUrl(fn ($record) => route('filament.admin.resources.report-resource.reports.view', ['record' => $record]))
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_report')),
                ]),
            ]);
    }
}
