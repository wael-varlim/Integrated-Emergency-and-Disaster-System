<?php

namespace App\Filament\Admin\Resources\ReportResource\Schemas;

use App\Models\Report;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function schema(Schema $schema): Schema
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
}
