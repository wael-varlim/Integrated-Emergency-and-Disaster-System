<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Tables;

use App\Models\NewsType;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class AwarenessArticleTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon_url')
                    ->label('Icon')
                    ->getStateUsing(function ($record) {
                        // Return the URL using url() helper (like Media model)
                        $iconPath = $record->icon_url;
                        
                        if ($iconPath && \Storage::disk('public')->exists($iconPath)) {
                            return url('/storage/' . $iconPath);
                        }
                        // Fallback to default
                        return url('/storage/awareness-icons/default.svg');
                    })
                    ->circular()
                    ->size(50),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('body')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('newsType.type_name')
                    ->label('News Type')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('news_type_id')
                    ->label('News Type')
                    ->options(NewsType::all()->pluck('type_name', 'id')),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_awareness_article')),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_awareness_article')),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_awareness_article')),
                ]),
            ]);
    }
}
