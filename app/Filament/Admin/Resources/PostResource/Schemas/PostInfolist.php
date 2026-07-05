<?php

namespace App\Filament\Admin\Resources\PostResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Post Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('by_admin')
                            ->label('Created by Admin')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->since(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->since(),
                    ])
                    ->columns(2),

                Section::make('Post Title')
                    ->schema([
                        TextEntry::make('english_title')
                            ->label('English')
                            ->placeholder('Not provided')
                            ->size('lg')
                            ->weight('bold')
                            ->state(function ($record) {
                                if (!$record) {
                                    return null;
                                }
                                
                                // Check if title is JSON
                                if ($record->title && str_starts_with(trim($record->title), '{')) {
                                    $titles = json_decode($record->title, true);
                                    return $titles['en'] ?? null;
                                }
                                
                                // Otherwise, title field IS the English title
                                return $record->title;
                            }),
                        TextEntry::make('arabic_title')
                            ->label('Arabic')
                            ->placeholder('Not provided')
                            ->size('lg')
                            ->weight('bold')
                            ->state(function ($record) {
                                if (!$record) {
                                    return null;
                                }
                                
                                // Check if title is JSON
                                if ($record->title && str_starts_with(trim($record->title), '{')) {
                                    $titles = json_decode($record->title, true);
                                    return $titles['ar'] ?? null;
                                }
                                
                                // Otherwise, get from postTranslations
                                if ($record->postTranslations) {
                                    $arTranslation = $record->postTranslations->firstWhere('language_code', 'ar');
                                    return $arTranslation?->translation;
                                }
                                
                                return null;
                            }),
                    ])
                    ->columns(2),

                Section::make('News Body')
                    ->schema([
                        TextEntry::make('news.body')
                            ->label('English')
                            ->placeholder('Not provided')
                            ->columnSpanFull(),
                        TextEntry::make('arabic_news_body')
                            ->label('Arabic')
                            ->placeholder('Not provided')
                            ->state(function ($record) {
                                if (!$record || !$record->news || !$record->news->newsTranslations) {
                                    return null;
                                }
                                $arTranslation = $record->news->newsTranslations->firstWhere('language_code', 'ar');
                                return $arTranslation?->translation;
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Location')
                    ->schema([
                        TextEntry::make('news.address.city.name')
                            ->label('City')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('news.address.street')
                            ->label('Street')
                            ->placeholder('Not specified'),
                    ])
                    ->columns(2),

                Section::make('Notification')
                    ->schema([
                        TextEntry::make('notification.title')
                            ->label('Title (English)')
                            ->placeholder('Not provided')
                            ->weight('bold'),
                        TextEntry::make('arabic_notification_title')
                            ->label('Title (Arabic)')
                            ->placeholder('Not provided')
                            ->weight('bold')
                            ->state(function ($record) {
                                if (!$record || !$record->notification || !$record->notification->notificationTranslations) {
                                    return null;
                                }
                                $arTranslation = $record->notification->notificationTranslations->firstWhere('language_code', 'ar');
                                return $arTranslation?->title_translation;
                            }),
                        TextEntry::make('notification.body')
                            ->label('Body (English)')
                            ->placeholder('Not provided'),
                        TextEntry::make('arabic_notification_body')
                            ->label('Body (Arabic)')
                            ->placeholder('Not provided')
                            ->state(function ($record) {
                                if (!$record || !$record->notification || !$record->notification->notificationTranslations) {
                                    return null;
                                }
                                $arTranslation = $record->notification->notificationTranslations->firstWhere('language_code', 'ar');
                                return $arTranslation?->body_translation;
                            }),
                        TextEntry::make('notification.region.city.name')
                            ->label('Target Region')
                            ->badge()
                            ->color('success')
                            ->placeholder('All regions')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->notification()->exists()),
            ]);
    }
}
