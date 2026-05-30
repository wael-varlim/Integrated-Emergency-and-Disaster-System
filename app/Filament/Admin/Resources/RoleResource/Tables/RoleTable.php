<?php

namespace App\Filament\Admin\Resources\RoleResource\Tables;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleTable
{
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
            ->recordActions([
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
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
