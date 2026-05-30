<?php

namespace App\Filament\Admin\Resources\SubAdminResource\Tables;

use App\Models\KnownUser;
use App\Models\User;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class SubAdminTable
{
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

                Tables\Columns\TextColumn::make('knownUser.official_identifier')
                    ->label('Official Identifier')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('knownUser.official_identifier_method')
                    ->label('ID Type')
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
            ->recordActions([
                Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (empty($data['password'])) {
                            unset($data['password']);
                        }
                        return $data;
                    }),
                Actions\DeleteAction::make()
                    ->after(function (User $record) {
                        $record->knownUser?->delete();
                    }),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
