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
                SchemaComponents\Section::make('Article Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Title')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'The article title is required.',
                                'max' => 'The title must not exceed 255 characters.',
                            ]),

                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(6)
                            ->label('Body')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'The article body is required.',
                            ]),

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
