<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubAdminResource\Pages;
use App\Models\KnownUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SubAdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Sub Admins';

    protected static ?string $modelLabel = 'Sub Admin';

    protected static ?string $pluralModelLabel = 'Sub Admins';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('manage_sub_admins') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_type', 'known')
            ->whereHas('roles')
            ->where('id', '!=', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(15)
                            ->label('First Name'),

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(15)
                            ->label('Last Name'),

                        Forms\Components\TextInput::make('national_number')
                            ->required()
                            ->maxLength(11)
                            ->label('National Number'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->label('Email'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Security')
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

                Forms\Components\Section::make('Roles & Access')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('knownUser.first_name')
                    ->label('First Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('knownUser.last_name')
                    ->label('Last Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('knownUser.email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('knownUser.national_number')
                    ->label('National Number')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('roles.name')
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
                    ->label('Roles'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filter by Role')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Remove password if empty on edit
                        if (empty($data['password'])) {
                            unset($data['password']);
                        }
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (User $record) {
                        $record->knownUser?->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSubAdmins::route('/'),
            'create' => Pages\CreateSubAdmin::route('/create'),
            'edit'   => Pages\EditSubAdmin::route('/{record}/edit'),
        ];
    }
}