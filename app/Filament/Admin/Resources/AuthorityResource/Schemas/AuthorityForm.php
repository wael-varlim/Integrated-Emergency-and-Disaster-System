<?php

namespace App\Filament\Admin\Resources\AuthorityResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class AuthorityForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Authority Details')
                    ->description('Enter the authority name in both languages')
                    ->schema([
                        Tabs::make('Name Translations')
                            ->tabs([
                                Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('name.ar')
                                            ->label('Authority Name (Arabic)')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                
                                Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('name.en')
                                            ->label('Authority Name (English)')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('authority_type_id')
                            ->label('Authority Type')
                            ->relationship('authorityType', 'type_name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
