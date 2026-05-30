<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuthorityResource\Pages;
use App\Models\Authority;
use App\Models\AuthorityType;
use Filament\Forms;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class AuthorityResource extends Resource
{
    protected static ?string $model = Authority::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Authorities';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_authority',
            'create_authority',
            'update_authority',
            'delete_authority',
        ]) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Authority Name')
                    ->description('Enter the authority name in English and Arabic')
                    ->schema([
                        Forms\Components\TextInput::make('authorityType.type_name')
                            ->label('Name (English)')
                            ->required()
                            ->maxLength(255)
                            ->dehydrated(true),
                    ]),

                SchemaComponents\Section::make('Translations')
                    ->description('Add translations for this authority')
                    ->schema([
                        Forms\Components\Repeater::make('authorityType.authorityTranslation')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('languahe_code')
                                    ->label('Language')
                                    ->options([
                                        'en' => '🇬🇧 English',
                                        'ar' => '🇸🇦 Arabic',
                                    ])
                                    ->required()
                                    ->distinct(),

                                Forms\Components\TextInput::make('translation')
                                    ->label('Translation')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Translation')
                            ->maxItems(2)
                            ->dehydrated(true)
                            ->default([
                                ['languahe_code' => 'en', 'translation' => ''],
                                ['languahe_code' => 'ar', 'translation' => ''],
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('authorityType.type_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('authorityType.authorityTranslation')
                    ->label('Arabic Name')
                    ->formatStateUsing(function ($record) {
                        return $record->authorityType
                            ?->authorityTranslation
                            ->where('languahe_code', 'ar')
                            ->first()
                            ?->translation ?? '—';
                    }),

                Tables\Columns\TextColumn::make('news_count')
                    ->counts('news')
                    ->badge()
                    ->color('info')
                    ->label('News'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('update_authority')),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_authority')),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_authority')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAuthorities::route('/'),
            'create' => Pages\CreateAuthority::route('/create'),
            'edit'   => Pages\EditAuthority::route('/{record}/edit'),
        ];
    }
}