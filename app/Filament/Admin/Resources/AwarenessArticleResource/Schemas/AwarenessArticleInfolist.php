<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AwarenessArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Article Information')
                    ->schema([
                        ImageEntry::make('icon_url')
                            ->label('Icon')
                            ->getStateUsing(function ($record) {
                                // Return the full URL using url() helper (like Media model)
                                $iconPath = $record->icon_url;
                                
                                if ($iconPath && \Storage::disk('public')->exists($iconPath)) {
                                    // Generate URL using url() helper instead of Storage::url()
                                    return url('/storage/' . $iconPath);
                                }
                                // Fallback to default
                                return url('/storage/awareness-icons/default.svg');
                            })
                            ->height(150)
                            ->width(150)
                            ->extraImgAttributes(['class' => 'rounded-lg shadow-md']),
                        
                        TextEntry::make('title')
                            ->label('Title')
                            ->weight('bold')
                            ->columnSpanFull(),
                        
                        TextEntry::make('newsType.type_name')
                            ->label('News Type')
                            ->badge()
                            ->color('success'),
                        
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Article Content')
                    ->schema([
                        TextEntry::make('body')
                            ->label('Body')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),
            ]);
    }
}
