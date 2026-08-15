<?php

namespace App\Filament\Admin\Resources\SuggestionResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SuggestionInfolist
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Suggestion')
                    ->schema([
                        TextEntry::make('content')
                            ->label('Content')
                            ->placeholder('No content provided')
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Section::make('Status & Timeline')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                IconEntry::make('is_read_by_admin')
                                    ->label('Read Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),

                                TextEntry::make('created_at')
                                    ->label('Submitted At')
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->icon('heroicon-o-clock')
                                    ->dateTime()
                                    ->tooltip(fn ($record) => $record->updated_at?->diffForHumans()),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}