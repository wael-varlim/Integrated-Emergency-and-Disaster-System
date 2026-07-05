<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Schemas;

use App\Models\NewsType;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;

class AwarenessArticleForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Tabs::make('Translations')
                    ->tabs([
                        SchemaComponents\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('translations.en.title')
                                    ->label('Title (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'The English title is required.',
                                    ]),

                                Forms\Components\Textarea::make('translations.en.body')
                                    ->label('Body (English)')
                                    ->required()
                                    ->rows(6)
                                    ->validationMessages([
                                        'required' => 'The English body is required.',
                                    ]),
                            ]),

                        SchemaComponents\Tabs\Tab::make('Arabic')
                            ->schema([
                                Forms\Components\TextInput::make('translations.ar.title')
                                    ->label('Title (Arabic)')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'The Arabic title is required.',
                                    ]),

                                Forms\Components\Textarea::make('translations.ar.body')
                                    ->label('Body (Arabic)')
                                    ->required()
                                    ->rows(6)
                                    ->validationMessages([
                                        'required' => 'The Arabic body is required.',
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),

                SchemaComponents\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('news_type_id')
                            ->label('News Type')
                            ->options(NewsType::all()->pluck('type_name', 'id'))
                            ->searchable()
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select a news type.',
                            ])
                            ->helperText('Select the category for this awareness article'),

                        Forms\Components\FileUpload::make('icon_url')
                            ->label('Icon')
                            ->image()
                            ->disk('public')
                            ->directory('awareness-icons')
                            ->downloadable()
                            ->openable()
                            ->imagePreviewHeight('150')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->required()
                            ->validationMessages([
                                'required' => 'An icon image is required.',
                                'max' => 'The icon must not exceed 2MB.',
                            ])
                            ->helperText('Upload an icon image for this article (max 2MB)'),
                    ])
                    ->columns(2),
            ]);
    }
}
