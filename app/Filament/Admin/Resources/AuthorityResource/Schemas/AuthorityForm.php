<?php

namespace App\Filament\Admin\Resources\AuthorityResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;

class AuthorityForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Authority Name')
                    ->description('Enter the authority name in English and Arabic')
                    ->schema([
                        Forms\Components\TextInput::make('authorityType.type_name')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(true),
                    ]),

                SchemaComponents\Section::make('Translations')
                    ->description('Add translations for this authority')
                    ->schema([
                        Forms\Components\Repeater::make('authorityType.authorityTranslation')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('languahe_code')
                                    ->label('Language')
                                    ->options([
                                        'en' => '🇬🇧 English',
                                        'ar' => '🇸🇦 Arabic',
                                    ])
                                    ->required()
                                    ->distinct(),

                                Forms\Components\TextInput::make('translation')
                                    ->label('Translation')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Translation')
                            ->maxItems(2)
                            ->dehydrated(true)
                            ->default([
                                ['languahe_code' => 'en', 'translation' => ''],
                                ['languahe_code' => 'ar', 'translation' => ''],
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
