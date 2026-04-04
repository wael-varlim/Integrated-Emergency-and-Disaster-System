<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GovernorateResource\Pages;
use App\Models\Governorate;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Locations';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_governorate', 'create_governorate', 'update_governorate', 'delete_governorate',
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
                            ->label('Governorate Name'),

                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(Region::all()->pluck('id', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Translations')
                    ->schema([
                        Forms\Components\Repeater::make('governorateTranslation')
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

                Tables\Columns\TextColumn::make('region_id')
                    ->label('Region ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('city_count')
                    ->counts('city')
                    ->label('Cities')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region_id')
                    ->label('Region')
                    ->options(Region::all()->pluck('id', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_governorate')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_governorate')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_governorate')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => GovernorateResource\Pages\ListGovernorates::route('/'),
            'create' => GovernorateResource\Pages\CreateGovernorate::route('/create'),
            'edit'   => GovernorateResource\Pages\EditGovernorate::route('/{record}/edit'),
        ];
    }
}