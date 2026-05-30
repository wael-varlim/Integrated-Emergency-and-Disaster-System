<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return false;
        // return auth()->user()?->hasPermissionTo('manage_roles') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Role Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash()
                            ->label('Role Name'),

                        Forms\Components\Hidden::make('guard_name')
                            ->default('web'),
                    ]),

                SchemaComponents\Section::make('Permissions')
                    ->description('Select what this role can do')
                    ->schema(static::getPermissionSchema())
                    ->collapsible(),
            ]);
    }

    protected static function getPermissionSchema(): array
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
            $sections[] = SchemaComponents\Fieldset::make($group)
                ->schema([
                    Forms\Components\CheckboxList::make('permissions')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin'             => 'danger',
                        'news_manager'      => 'info',
                        'report_manager'    => 'warning',
                        'content_manager'   => 'success',
                        'authority_manager' => 'primary',
                        'viewer'            => 'gray',
                        default             => 'secondary',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->badge()
                    ->label('Permissions'),

                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->badge()
                    ->color('success')
                    ->label('Users'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Actions\DeleteAction $action, Role $record) {
                        if ($record->name === 'admin') {
                            Notification::make()
                                ->title('Cannot delete the admin role')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRoles::route('/'),
        ];
    }
}