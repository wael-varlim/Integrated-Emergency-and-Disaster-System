<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_report',
            'delete_report',
        ]) ?? false;
    }

    // No form needed - admin can only browse and delete
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Placeholder::make('news_body')
                ->label('News Content')
                ->content(fn (Report $record) => $record->news?->body ?? '—'),

            Placeholder::make('address')
                ->label('Address')
                ->content(fn (Report $record) => $record->news?->address?->street ?? '—'),

            Placeholder::make('reported_by')
                ->label('Reported By')
                ->content(fn (Report $record) =>
                    $record->news?->user?->knownUser
                        ? $record->news->user->knownUser->first_name . ' ' . $record->news->user->knownUser->last_name
                        : 'Anonymous'
                ),

            Placeholder::make('has_post')
                ->label('Has Post')
                ->content(fn (Report $record) => $record->news?->post ? 'Yes' : 'No'),

            Placeholder::make('created_at')
                ->label('Reported At')
                ->content(fn (Report $record) => $record->created_at?->toDateTimeString() ?? '—'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                InfolistSection::make('Report Details')
                    ->schema([
                        TextEntry::make('news.body')
                            ->label('News Content'),

                        TextEntry::make('news.address.street')
                            ->label('Address')
                            ->placeholder('—'),

                        TextEntry::make('news.user.knownUser.first_name')
                            ->label('Reported By')
                            ->formatStateUsing(fn ($record) =>
                                $record->news?->user?->knownUser
                                    ? $record->news->user->knownUser->first_name . ' ' . $record->news->user->knownUser->last_name
                                    : 'Anonymous'
                            ),

                        TextEntry::make('created_at')
                            ->label('Reported At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                InfolistSection::make('Post Status')
                    ->schema([
                        TextEntry::make('news.post.id')
                            ->label('Has Post')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                            ->badge()
                            ->color(fn ($record) => $record->news?->post ? 'success' : 'danger'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('news.body')
                    ->label('News')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->news?->body),

                Tables\Columns\TextColumn::make('news.address.street')
                    ->label('Address')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('news.user.knownUser.first_name')
                    ->label('Reported By')
                    ->formatStateUsing(fn ($record) =>
                        $record->news?->user?->knownUser
                            ? $record->news->user->knownUser->first_name . ' ' . $record->news->user->knownUser->last_name
                            : 'Anonymous'
                    ),

                Tables\Columns\IconColumn::make('news.post')
                    ->label('Has Post')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn ($record) => $record->news?->post !== null),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Reported At'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('has_post')
                    ->label('Has Post')
                    ->query(fn ($query) => $query->whereHas('news.post')),

                Tables\Filters\Filter::make('no_post')
                    ->label('No Post Yet')
                    ->query(fn ($query) => $query->whereDoesntHave('news.post')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasPermissionTo('delete_report')),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasPermissionTo('delete_report')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
        ];
    }
}
