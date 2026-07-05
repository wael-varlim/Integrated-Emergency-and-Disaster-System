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
                            ->label('Governorate Name')
                            ->validationMessages([
                                'required' => 'The governorate name is required.',
                                'max' => 'The governorate name must not exceed 255 characters.',
                            ]),

                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(Region::all()->pluck('id', 'id'))
                            ->searchable()
                            ->required()
                            ->validationMessages([
                                'required' => 'Please select a region.',
                            ])
                            ->helperText('Select the region where this governorate is located'),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Translations')
                    ->schema([
                        Forms\Components\Repeater::make('governorateTranslation')
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
