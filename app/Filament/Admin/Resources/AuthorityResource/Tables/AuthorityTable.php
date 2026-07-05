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

                Tables\Columns\TextColumn::make('name')
                    ->label('Name (English)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('arabic_name')
                    ->label('Name (Arabic)')
                    ->state(function ($record) {
                        return $record->authorityTranslation()
                            ->where('language_code', 'ar')
                            ->first()
                            ?->translation ?? '—';
                    })
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('authorityTranslation', function ($q) use ($search) {
                            $q->where('language_code', 'ar')
                              ->where('translation', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('authorityType.type_name')
                    ->label('Type')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

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
            ->filters([
                Tables\Filters\SelectFilter::make('authority_type_id')
                    ->label('Authority Type')
                    ->relationship('authorityType', 'type_name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('has_news')
                    ->label('Has News')
                    ->query(fn ($query) => $query->has('news')),

                Tables\Filters\Filter::make('no_news')
                    ->label('No News')
                    ->query(fn ($query) => $query->doesntHave('news')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
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
