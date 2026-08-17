<?php

namespace App\Filament\Admin\Resources\PostResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class PostForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Post Details')
                    ->schema([
                        Tabs::make('Post Translations')
                            ->tabs([
                                Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('title.ar')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Title (Arabic)'),
                                    ]),
                                
                                Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title.en')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Title (English)'),
                                    ]),
                            ])
                            ->columnSpanFull(),

                               Tabs::make('News Body Translations')
                            ->tabs([
                                Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\Textarea::make('news_body.ar')
                                            ->required()
                                            ->rows(5)
                                            ->label('News Body (Arabic)'),
                                    ]),
                                
                                Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\Textarea::make('news_body.en')
                                            ->required()
                                            ->rows(5)
                                            ->label('News Body (English)'),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('new_images')
                            ->label('Images')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('post-images')
                            ->visibility('public')
                            ->reorderable()
                            ->panelLayout('grid')
                            ->helperText('Existing images appear below and can be removed. Add up to 5 total. Max size: 5MB per image.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Notification Details')
                    ->description('A notification will be created for ALL regions')
                    ->schema([
                        Tabs::make('Notification Translations')
                            ->tabs([
                                Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('notification_title.ar')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Notification Title (Arabic)'),
                                        
                                        Forms\Components\Textarea::make('notification_body.ar')
                                            ->required()
                                            ->rows(3)
                                            ->label('Notification Body (Arabic)'),
                                    ]),
                                
                                Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('notification_title.en')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Notification Title (English)'),
                                        
                                        Forms\Components\Textarea::make('notification_body.en')
                                            ->required()
                                            ->rows(3)
                                            ->label('Notification Body (English)'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}