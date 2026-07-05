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
                                $iconPath = $record->icon_url;
                                
                                if ($iconPath && \Storage::disk('public')->exists($iconPath)) {
                                    return url('/storage/' . $iconPath);
                                }
                                return url('/storage/awareness-icons/default.svg');
                            })
                            ->height(150)
                            ->width(150)
                            ->extraImgAttributes(['class' => 'rounded-lg shadow-md']),
                        
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

                Section::make('English Translation')
                    ->schema([
                        TextEntry::make('title_en')
                            ->label('Title (English)')
                            ->getStateUsing(function ($record) {
                                $translation = $record->translations->firstWhere('language_code', 'en');
                                return $translation?->title ?? 'N/A';
                            })
                            ->weight('bold')
                            ->columnSpanFull(),
                        
                        TextEntry::make('body_en')
                            ->label('Body (English)')
                            ->getStateUsing(function ($record) {
                                $translation = $record->translations->firstWhere('language_code', 'en');
                                return $translation?->body ?? 'N/A';
                            })
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                Section::make('Arabic Translation')
                    ->schema([
                        TextEntry::make('title_ar')
                            ->label('Title (Arabic)')
                            ->getStateUsing(function ($record) {
                                $translation = $record->translations->firstWhere('language_code', 'ar');
                                return $translation?->title ?? 'N/A';
                            })
                            ->weight('bold')
                            ->columnSpanFull(),
                        
                        TextEntry::make('body_ar')
                            ->label('Body (Arabic)')
                            ->getStateUsing(function ($record) {
                                $translation = $record->translations->firstWhere('language_code', 'ar');
                                return $translation?->body ?? 'N/A';
                            })
                            ->columnSpanFull()
                            ->markdown(),
                    ]),
            ]);
    }
}
