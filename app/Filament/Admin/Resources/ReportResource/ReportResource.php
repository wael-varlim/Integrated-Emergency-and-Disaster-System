<?php

namespace App\Filament\Admin\Resources\ReportResource;

use App\Filament\Admin\Resources\ReportResource\Pages;
use App\Filament\Admin\Resources\ReportResource\Schemas\ReportForm;
use App\Filament\Admin\Resources\ReportResource\Tables\ReportTable;
use App\Models\Report;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Table;

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
        return ReportForm::schema($schema);
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
        return ReportTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReports::route('/'),
        ];
    }
}
