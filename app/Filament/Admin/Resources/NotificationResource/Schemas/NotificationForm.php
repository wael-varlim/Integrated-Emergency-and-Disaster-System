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
                SchemaComponents\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Title')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(4)
                            ->label('Body')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(Region::with('city')->get()->pluck('city.name', 'id')->filter())
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
