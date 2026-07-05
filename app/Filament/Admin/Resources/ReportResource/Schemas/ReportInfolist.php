<?php

namespace App\Filament\Admin\Resources\ReportResource\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Report Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Report ID'),
                        
                        TextEntry::make('created_at')
                            ->label('Reported At')
                            ->dateTime()
                            ->badge()
                            ->color('info'),
                        
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime()
                            ->since(),
                    ])
                    ->columns(3),

                Section::make('News Details')
                    ->schema([
                        TextEntry::make('news.body')
                            ->label('News Content')
                            ->formatStateUsing(fn ($state) => mb_convert_encoding($state ?? '', 'UTF-8', 'UTF-8'))
                            ->html()
                            ->columnSpanFull(),
                        
                        TextEntry::make('news.newsType.name')
                            ->label('News Types')
                            ->badge()
                            ->color('warning')
                            ->separator(','),
                        
                        TextEntry::make('news.created_at')
                            ->label('News Created At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Reporter Information')
                    ->schema([
                        TextEntry::make('news.knownUser.first_name')
                            ->label('First Name')
                            ->formatStateUsing(fn ($state) => $state ? mb_convert_encoding($state, 'UTF-8', 'UTF-8') : 'Anonymous'),
                        
                        TextEntry::make('news.knownUser.last_name')
                            ->label('Last Name')
                            ->formatStateUsing(fn ($state) => $state ? mb_convert_encoding($state, 'UTF-8', 'UTF-8') : '—'),
                        
                        TextEntry::make('news.knownUser.email')
                            ->label('Email')
                            ->copyable()
                            ->default('—'),
                        
                        TextEntry::make('news.knownUser.official_identifier')
                            ->label('Official ID')
                            ->copyable()
                            ->default('—'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Location Details')
                    ->schema([
                        TextEntry::make('news.address.street')
                            ->label('Street Address')
                            ->formatStateUsing(fn ($state) => $state ? mb_convert_encoding($state, 'UTF-8', 'UTF-8') : '—')
                            ->columnSpanFull(),
                        
                        TextEntry::make('news.address.city.name')
                            ->label('City')
                            ->badge()
                            ->color('success')
                            ->formatStateUsing(fn ($state) => $state ? mb_convert_encoding($state, 'UTF-8', 'UTF-8') : '—'),
                        
                        TextEntry::make('news.address.city.governorate.name')
                            ->label('Governorate')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn ($state) => $state ? mb_convert_encoding($state, 'UTF-8', 'UTF-8') : '—'),
                        
                        TextEntry::make('coordinates')
                            ->label('GPS Coordinates')
                            ->copyable()
                            ->icon('heroicon-o-map-pin')
                            ->formatStateUsing(fn ($state, $record) => $state ?: '—')
                            ->url(fn ($record) => $record->google_maps_link)
                            ->openUrlInNewTab()
                            ->tooltip('Click to view on Google Maps'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Media Attachments')
                    ->schema([
                        RepeatableEntry::make('news.media')
                            ->label('')
                            ->schema([
                                TextEntry::make('mediaType.type_name')
                                    ->label('Media Type')
                                    ->badge()
                                    ->color(fn ($state) => match(strtolower($state ?? '')) {
                                        'image' => 'success',
                                        'video' => 'warning',
                                        'audio', 'voice' => 'info',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn ($state) => ucfirst($state ?? 'Unknown')),
                                
                                // Image Preview
                                ImageEntry::make('full_url')
                                    ->label('Image')
                                    ->visible(fn ($record) => strtolower($record->mediaType->type_name ?? '') === 'image')
                                    ->height(220)
                                    ->extraImgAttributes(['loading' => 'lazy', 'class' => 'rounded-lg shadow-md'])
                                    ->columnSpanFull(),
                                
                                // Video Player
                                TextEntry::make('full_url')
                                    ->label('Video Player')
                                    ->visible(fn ($record) => strtolower($record->mediaType->type_name ?? '') === 'video')
                                    ->formatStateUsing(fn ($state) => new \Illuminate\Support\HtmlString(
                                        '<video controls class="w-full rounded-lg shadow-md" style="max-height: 400px;">
                                            <source src="' . $state . '" type="video/mp4">
                                            <source src="' . $state . '" type="video/webm">
                                            <source src="' . $state . '" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                        <a href="' . $state . '" target="_blank" class="text-primary-600 hover:underline text-sm mt-2 inline-block">Download Video</a>'
                                    ))
                                    ->columnSpanFull(),
                                
                                // Audio Player
                                TextEntry::make('full_url')
                                    ->label('Audio Player')
                                    ->visible(fn ($record) => in_array(strtolower($record->mediaType->type_name ?? ''), ['audio', 'voice']))
                                    ->formatStateUsing(fn ($state) => new \Illuminate\Support\HtmlString(
                                        '<audio controls class="w-full rounded-lg shadow-md">
                                            <source src="' . $state . '" type="audio/mpeg">
                                            <source src="' . $state . '" type="audio/ogg">
                                            <source src="' . $state . '" type="audio/wav">
                                            Your browser does not support the audio tag.
                                        </audio>
                                        <a href="' . $state . '" target="_blank" class="text-primary-600 hover:underline text-sm mt-2 inline-block">Download Audio</a>'
                                    ))
                                    ->columnSpanFull(),
                                
                                TextEntry::make('created_at')
                                    ->label('Uploaded At')
                                    ->dateTime()
                                    ->since(),
                            ])
                            ->columns(2)
                            ->contained(true),
                    ])
                    ->visible(fn ($record) => $record->news?->media?->count() > 0)
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Authorities Notified')
                    ->schema([
                        RepeatableEntry::make('news.authority')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Authority Name')
                                    ->weight('bold'),
                                
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->copyable()
                                    ->icon('heroicon-o-envelope'),
                                
                                TextEntry::make('phone_number')
                                    ->label('Phone')
                                    ->copyable()
                                    ->icon('heroicon-o-phone'),
                                
                                TextEntry::make('address')
                                    ->label('Address')
                                    ->default('—')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ])
                    ->visible(fn ($record) => $record->news?->authority?->count() > 0)
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Post Status')
                    ->schema([
                        TextEntry::make('news.post.id')
                            ->label('Published as Post')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes - Post ID: ' . $state : 'Not Published')
                            ->badge()
                            ->color(fn ($record) => $record->news?->post ? 'success' : 'gray'),
                        
                        TextEntry::make('news.post.by_admin')
                            ->label('Published By')
                            ->formatStateUsing(fn ($state) => $state ? 'Admin' : 'User')
                            ->badge()
                            ->color(fn ($state) => $state ? 'info' : 'warning')
                            ->visible(fn ($record) => $record->news?->post !== null),
                        
                        TextEntry::make('news.post.created_at')
                            ->label('Published At')
                            ->dateTime()
                            ->visible(fn ($record) => $record->news?->post !== null),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
