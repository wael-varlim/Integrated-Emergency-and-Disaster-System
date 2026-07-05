<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('User ID'),
                        
                        TextEntry::make('user_type')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'known' => 'success',
                                'anonymous' => 'gray',
                                default => 'warning',
                            }),
                        
                        TextEntry::make('created_at')
                            ->label('Registered At')
                            ->dateTime(),
                    ])
                    ->columns(3),

                Section::make('Known User Details')
                    ->schema([
                        TextEntry::make('knownUser.first_name')
                            ->label('First Name'),
                        
                        TextEntry::make('knownUser.last_name')
                            ->label('Last Name'),
                        
                        TextEntry::make('knownUser.email')
                            ->label('Email')
                            ->copyable(),
                        
                        TextEntry::make('knownUser.official_identifier')
                            ->label('Official ID')
                            ->copyable(),
                        
                        TextEntry::make('knownUser.official_identifier_method')
                            ->label('ID Method'),
                        
                        TextEntry::make('knownUser.is_verified')
                            ->label('Verified')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                            ->color(fn ($state) => $state ? 'success' : 'warning'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record->knownUser !== null),

                Section::make('Anonymous User Details')
                    ->schema([
                        TextEntry::make('anonymousUser.identifier')
                            ->label('Anonymous Identifier')
                            ->copyable(),
                    ])
                    ->visible(fn ($record) => $record->anonymousUser !== null),

                Section::make('Roles & Permissions')
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label('Assigned Roles')
                            ->badge()
                            ->separator(',')
                            ->color('info')
                            ->default('No roles assigned'),
                    ])
                    ->visible(fn ($record) => $record->roles()->exists()),
            ]);
    }
}
