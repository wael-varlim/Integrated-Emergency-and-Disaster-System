<?php

namespace App\Filament\Admin\Resources\SuggestionResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;

class SuggestionForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->rows(5)
                            ->label('Content')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_read_by_admin')
                            ->label('Mark as Read')
                            ->default(false),
                    ]),
            ]);
    }
}
