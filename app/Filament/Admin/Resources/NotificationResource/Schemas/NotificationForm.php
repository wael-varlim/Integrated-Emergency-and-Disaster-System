<?php

namespace App\Filament\Admin\Resources\NotificationResource\Schemas;

use App\Models\Region;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;

class NotificationForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Notification Content')
                    ->description('Enter the notification title and body in English')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Title (English)')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'The notification title is required.',
                                'max' => 'The title must not exceed 255 characters.',
                            ]),

                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(4)
                            ->label('Body (English)')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'The notification body is required.',
                            ]),

                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(Region::with('city')->get()->pluck('city.name', 'id')->filter())
                            ->searchable()
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select a region.',
                            ])
                            ->helperText('Select the region that will receive this notification'),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Translations')
                    ->description('Add translations for this notification')
                    ->schema([
                        Forms\Components\Repeater::make('notificationTranslations')
                            ->label('')
                            ->relationship('notificationTranslations')
                            ->schema([
                                Forms\Components\Select::make('language_code')
                                    ->label('Language')
                                    ->options([
                                        'en' => '🇬🇧 English',
                                        'ar' => '🇸🇦 Arabic',
                                    ])
                                    ->required()
                                    ->distinct(),

                                Forms\Components\TextInput::make('title_translation')
                                    ->label('Title Translation')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('body_translation')
                                    ->label('Body Translation')
                                    ->required()
                                    ->rows(3),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add Translation')
                            ->maxItems(2)
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                isset($state['language_code']) 
                                    ? ($state['language_code'] === 'en' ? '🇬🇧 English' : '🇸🇦 Arabic')
                                    : null
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
