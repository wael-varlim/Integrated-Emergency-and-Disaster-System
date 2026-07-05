<?php

namespace App\Filament\Admin\Resources\CityResource\Schemas;

use App\Models\Governorate;
use App\Models\Region;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class CityForm
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
                            ->label('City Name')
                            ->validationMessages([
                                'required' => 'The city name is required.',
                                'max' => 'The city name must not exceed 255 characters.',
                            ]),

                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(Region::all()->pluck('id', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->validationMessages([
                                'required' => 'Please select a region.',
                            ])
                            ->helperText('Select the region first to filter governorates'),

                        Forms\Components\Select::make('governorate_id')
                            ->label('Governorate')
                            ->options(fn (Get $get) => Governorate::where('region_id', $get('region_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select a governorate.',
                            ])
                            ->helperText('Must belong to the selected region'),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Translations')
                    ->schema([
                        Forms\Components\Repeater::make('cityTranslation')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('language_code')
                                    ->label('Language')
                                    ->options([
                                        'en' => 'English',
                                        'ar' => 'Arabic',
                                    ])
                                    ->required()
                                    ->distinct()
                                    ->validationMessages([
                                        'distinct' => 'Each language can only be added once.',
                                    ]),

                                Forms\Components\TextInput::make('translation')
                                    ->label('Translation')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Translation')
                            ->label(''),
                    ])
                    ->collapsible(),
            ]);
    }
}
