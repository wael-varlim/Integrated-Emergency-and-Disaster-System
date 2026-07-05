<?php

namespace App\Filament\Admin\Resources\NotificationResource;

use App\Filament\Admin\Resources\NotificationResource\Pages;
use App\Filament\Admin\Resources\NotificationResource\Schemas\NotificationForm;
use App\Filament\Admin\Resources\NotificationResource\Tables\NotificationTable;
use App\Models\Notification;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_any_notification', 'create_notification',
            'update_notification', 'delete_notification',
        ]) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return NotificationForm::schema($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Notification Details')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('body')
                            ->label('Body')
                            ->columnSpanFull(),
                        TextEntry::make('region.city.name')
                            ->label('Region')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('post.title')
                            ->label('Related Post')
                            ->default('—'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Translations')
                    ->schema([
                        RepeatableEntry::make('notificationTranslations')
                            ->label('')
                            ->schema([
                                TextEntry::make('language_code')
                                    ->label('Language')
                                    ->formatStateUsing(fn ($state) => match($state) {
                                        'en' => '🇬🇧 English',
                                        'ar' => '🇸🇦 Arabic',
                                        default => strtoupper($state),
                                    })
                                    ->badge(),
                                TextEntry::make('title_translation')
                                    ->label('Title'),
                                TextEntry::make('body_translation')
                                    ->label('Body')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->visible(fn ($record) => $record->notificationTranslations()->exists())
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return NotificationTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNotifications::route('/'),
        ];
    }
}