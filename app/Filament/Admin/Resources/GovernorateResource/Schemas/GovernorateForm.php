<?php

namespace App\Filament\Admin\Resources\GovernorateResource\Schemas;

use App\Models\Region;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;

class GovernorateForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Governorate Name'),

                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(Region::all()->pluck('id', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Translations')
                    ->schema([
                        Forms\Components\Repeater::make('governorateTranslation')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('languahe_code')
                                    ->label('Language')
                                    ->options([
                                        'en' => 'English',
                                        'ar' => 'Arabic',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('translation')
                                    ->label('Translation')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Translation')
                            ->label(''),
                    ])
                    ->collapsible(),
            ]);
    }
}
