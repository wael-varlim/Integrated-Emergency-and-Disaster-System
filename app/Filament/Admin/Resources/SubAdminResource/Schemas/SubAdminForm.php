<?php

namespace App\Filament\Admin\Resources\SubAdminResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class SubAdminForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(15)
                            ->dehydrated(true)
                            ->label('First Name'),

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(15)
                            ->dehydrated(true)
                            ->label('Last Name'),

                        Forms\Components\TextInput::make('official_identifier')
                            ->required()
                            ->maxLength(11)
                            ->dehydrated(true)
                            ->label('Official Identifier'),

                        Forms\Components\Select::make('official_identifier_method')
                            ->required()
                            ->dehydrated(true)
                            ->label('ID Type')
                            ->options([
                                'national_id' => 'National ID',
                                'passport' => 'Passport',
                                'driver_license' => 'Driver License',
                            ]),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->dehydrated(true)
                            ->label('Email'),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Security')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->minLength(8)
                            ->label('Password')
                            ->helperText(fn (string $operation) => $operation === 'edit'
                                ? 'Leave empty to keep current password'
                                : null),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->requiredWith('password')
                            ->dehydrated(false)
                            ->label('Confirm Password'),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Roles & Access')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->options(
                                Role::where('guard_name', 'web')
                                    ->pluck('name', 'id')
                            )
                            ->columns(3)
                            ->required()
                            ->label('Assign Roles')
                            ->descriptions(
                                Role::where('guard_name', 'web')
                                    ->get()
                                    ->mapWithKeys(fn ($role) => [
                                        $role->id => $role->permissions->count() . ' permissions',
                                    ])
                                    ->toArray()
                            ),
                    ]),
            ]);
    }
}
