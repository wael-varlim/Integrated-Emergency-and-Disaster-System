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

                Tables\Columns\TextColumn::make('user_roles')
                    ->label('Roles')
                    ->state(function ($record) {
                        $roles = $record->roles;
                        if ($roles->isEmpty()) {
                            return '—';
                        }
                        
                        $limit = 2; // Show only first 2 roles
                        $visible = $roles->take($limit);
                        $remaining = $roles->count() - $limit;
                        
                        $colorMap = [
                            'admin'                => '--danger',
                            'posts_manager'        => '--success',
                            'awareness_manager'    => '--info',
                            'report_manager'       => '--warning',
                            'suggestion_manager'   => '--purple',
                            'authority_manager'    => '--primary',
                            'viewer'               => '--gray',
                        ];
                        
                        $html = '<div class="flex flex-wrap gap-1">';
                        foreach ($visible as $role) {
                            $colorVar = $colorMap[$role->name] ?? '--gray';
                            $html .= '<span class="fi-badge fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30" style="--c-50:var(' . $colorVar . '-50);--c-400:var(' . $colorVar . '-400);--c-600:var(' . $colorVar . '-600);">' 
                                  . e($role->name) 
                                  . '</span>';
                        }
                        
                        if ($remaining > 0) {
                            $html .= '<span class="fi-badge fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30" style="--c-50:var(--gray-50);--c-400:var(--gray-400);--c-600:var(--gray-600);">+' 
                                  . $remaining 
                                  . ' more</span>';
                        }
                        $html .= '</div>';
                        
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->tooltip(fn ($record) => $record->roles->pluck('name')->join(', '))
                    ->html()
                    ->searchable(query: function ($query, $search) {
                        return $query->whereHas('roles', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    }),

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
