<?php

namespace App\Filament\Admin\Resources\SubAdminResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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

                        Forms\Components\Select::make('official_identifier_method')
                            ->required()
                            ->dehydrated(true)
                            ->label('ID Type')
                            ->options([
                                'national_id' => 'National ID',
                                'passport' => 'Passport',
                            ])
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                // Clear the official_identifier when ID type changes
                                $set('official_identifier', null);
                            }),

                        Forms\Components\TextInput::make('official_identifier')
                            ->required()
                            ->dehydrated(true)
                            ->label('Official Identifier')
                            ->unique(table: 'known_users', column: 'official_identifier', ignoreRecord: true)
                            ->rules(function (Get $get) {
                                $method = $get('official_identifier_method');
                                
                                if ($method === 'national_id') {
                                    return [
                                        'required',
                                        'string',
                                        'min:6',
                                        'max:11',
                                        'regex:/^[A-Za-z0-9]+$/', // Alphanumeric only
                                    ];
                                } elseif ($method === 'passport') {
                                    return [
                                        'required',
                                        'string',
                                        'min:6',
                                        'max:9',
                                        'regex:/^[A-Z0-9]+$/', // Uppercase letters and numbers only
                                    ];
                                }
                                
                                return ['required', 'string', 'max:11'];
                            })
                            ->validationMessages([
                                'unique' => 'This official identifier is already registered.',
                                'regex' => 'The official identifier format is invalid.',
                                'min' => 'The official identifier must be at least :min characters.',
                                'max' => 'The official identifier must not exceed :max characters.',
                            ])
                            ->helperText(function (Get $get) {
                                $method = $get('official_identifier_method');
                                
                                if ($method === 'national_id') {
                                    return 'National ID: 6-11 characters, letters and numbers allowed.';
                                } elseif ($method === 'passport') {
                                    return 'Passport: 6-9 characters, uppercase letters and numbers only.';
                                }
                                
                                return 'Select ID Type first to see format requirements.';
                            })
                            ->placeholder(function (Get $get) {
                                $method = $get('official_identifier_method');
                                
                                if ($method === 'national_id') {
                                    return 'e.g., ABC123456';
                                } elseif ($method === 'passport') {
                                    return 'e.g., A12345678';
                                }
                                
                                return 'Enter identifier';
                            })
                            ->live()
                            ->afterStateUpdated(function ($livewire, $state) {
                                // Clear validation error for this field when user types
                                $livewire->resetErrorBag('data.official_identifier');
                            }),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->dehydrated(true)
                            ->unique(table: 'known_users', column: 'email', ignoreRecord: true)
                            ->label('Email')
                            ->validationMessages([
                                'unique' => 'This email address is already registered.',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($livewire, $state) {
                                // Clear validation error for this field when user types
                                $livewire->resetErrorBag('data.email');
                            }),
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
                                    ->whereNotIn('name', ['mobile_user', 'news_manager', 'content_manager']) // Exclude deprecated roles
                                    ->pluck('name', 'id')
                                    ->mapWithKeys(fn ($name, $id) => [
                                        $id => ucwords(str_replace('_', ' ', $name))
                                    ])
                            )
                            ->columns(2)
                            ->required()
                            ->label('Assign Roles')
                            ->helperText('Select one or more roles to assign to this user. If Admin is selected, other roles are not needed.')
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                // Get the admin role ID
                                $adminRole = Role::where('name', 'admin')->first();
                                
                                // If admin role is checked, uncheck all others
                                if ($adminRole && in_array($adminRole->id, $state ?? [])) {
                                    $set('roles', [$adminRole->id]);
                                }
                            })
                            ->disableOptionWhen(function (Get $get, $value) {
                                // Get the admin role ID
                                $adminRole = Role::where('name', 'admin')->first();
                                
                                if (!$adminRole) {
                                    return false;
                                }
                                
                                $selectedRoles = $get('roles') ?? [];
                                
                                // If admin is selected and this is not admin, disable it
                                if (in_array($adminRole->id, $selectedRoles) && $value !== $adminRole->id) {
                                    return true;
                                }
                                
                                return false;
                            })
                            ->descriptions(
                                Role::where('guard_name', 'web')
                                    ->whereNotIn('name', ['mobile_user', 'news_manager', 'content_manager'])
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
