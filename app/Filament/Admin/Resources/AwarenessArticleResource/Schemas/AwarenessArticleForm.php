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
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(6)
                            ->label('Body')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('news_type_id')
                            ->label('News Type')
                            ->options(NewsType::all()->pluck('type_name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\FileUpload::make('icon_url')
                            ->label('Icon')
                            ->image()
                            ->disk('public')
                            ->directory('awareness-icons')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
