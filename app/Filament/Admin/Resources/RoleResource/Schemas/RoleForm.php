<?php

namespace App\Filament\Admin\Resources\RoleResource\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Role Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash()
                            ->label('Role Name'),

                        Hidden::make('guard_name')
                            ->default('web'),
                    ]),

                Section::make('Permissions')
                    ->description('Select what this role can do')
                    ->schema(self::getPermissionSchema())
                    ->collapsible(),
            ]);
    }

    public static function getPermissionSchema(): array
    {
        $permissions = Permission::where('guard_name', 'web')
            ->pluck('name', 'id')
            ->toArray();

        $grouped = [];

        foreach ($permissions as $id => $name) {
            $parts = explode('_', $name);

            if ($parts[0] === 'manage') {
                $grouped['Admin'][$id] = $name;
                continue;
            }

            if (isset($parts[1]) && $parts[1] === 'any') {
                $model = implode('_', array_slice($parts, 2));
            } else {
                $model = implode('_', array_slice($parts, 1));
            }

            $grouped[ucwords(str_replace('_', ' ', $model))][$id] = $name;
        }

        $sections = [];

        foreach ($grouped as $group => $perms) {
            $sections[] = Fieldset::make($group)
                ->schema([
                    CheckboxList::make('permissions')
                        ->relationship('permissions', 'name')
                        ->options($perms)
                        ->columns(3)
                        ->label('')
                        ->bulkToggleable(),
                ])
                ->columns(1);
        }

        return $sections;
    }
}
