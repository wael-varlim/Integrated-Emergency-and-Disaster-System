<?php

namespace App\Filament\Admin\Resources\PostResource\Schemas;

use App\Models\Region;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Post Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                  
                        Textarea::make('news_body')
                            ->required()
                            ->label('News Body')
                            ->rows(5)
                            ->dehydrated(true),
                    ]),
                

                Section::make('Notification Details')
                    ->description('A notification will be created for this post')
                    ->schema([
                        TextInput::make('notification_title')
                            ->label('Notification Title')
                            ->required()
                            ->dehydrated(true),
                        Textarea::make('notification_body')
                            ->label('Notification Body')
                            ->required()
                            ->rows(3)
                            ->dehydrated(true),
                        Select::make('region_id')
                            ->label('Target Region')
                            ->options(Region::with('city')->get()->pluck('city.name', 'id')->filter())
                            ->searchable()
                            ->required()
                            ->dehydrated(true),
                    ]),
            ]);
    }
}
