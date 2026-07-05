<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('knownUser.first_name')
                    ->label('First Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('knownUser.last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('knownUser.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                
                TextColumn::make('user_roles')
                    ->label('Roles')
                    ->state(function ($record) {
                        $roles = $record->roles;
                        if ($roles->isEmpty()) {
                            return '—';
                        }
                        
                        $limit = 2; // Show only first 2 roles
                        $visible = $roles->take($limit);
                        $remaining = $roles->count() - $limit;
                        
                        $html = '<div class="flex flex-wrap gap-1">';
                        foreach ($visible as $role) {
                            $html .= '<span class="fi-badge fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30" style="--c-50:var(--info-50);--c-400:var(--info-400);--c-600:var(--info-600);">' 
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
                
                TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
