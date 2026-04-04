<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CityResource\Pages;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_city', 'create_city', 'update_city', 'delete_city',
        ]) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('City Name'),

                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(Region::all()->pluck('id', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('governorate_id')
                            ->label('Governorate')
                            ->options(fn ($get) => Governorate::where('region_id', $get('region_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Translations')
                    ->schema([
                        Forms\Components\Repeater::make('cityTranslation')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('languahe_code')
                                    ->label('Language')
                                    ->options([
                                        'en' => 'English',
                                        'ar' => 'Arabic',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('translation')
                                    ->label('Translation')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Translation')
                            ->label(''),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('governorate.name')
                    ->label('Governorate')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('region_id')
                    ->label('Region ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('governorate_id')
                    ->label('Governorate')
                    ->options(Governorate::all()->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_city')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_city')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_city')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit'   => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}