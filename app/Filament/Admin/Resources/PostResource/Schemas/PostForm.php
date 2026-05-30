<?php

namespace App\Filament\Admin\Resources\PostResource\Schemas;

use App\Models\City;
use App\Models\Region;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

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
                        TextInput::make('owner_role')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('News Information')
                    ->schema([
                        Textarea::make('news_body')
                            ->required()
                            ->label('News Body')
                            ->rows(5)
                            ->dehydrated(true),

                        Select::make('city_id')
                            ->label('City')
                            ->options(City::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->dehydrated(true),
                        TextInput::make('street')
                            ->label('Street')
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(true),
                    ]),

                Section::make('Optional Notification')
                    ->schema([
                        Toggle::make('create_notification')
                            ->label('Create a notification for this post?')
                            ->live()
                            ->default(false)
                            ->dehydrated(true),
                        Group::make()
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
                            ])
                            ->visible(fn (Get $get) => $get('create_notification') === true),
                    ]),
            ]);
    }
}
