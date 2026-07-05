<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Pages;

use App\Filament\Admin\Resources\AwarenessArticleResource\AwarenessArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAwarenessArticles extends ListRecords
{
    protected static string $resource = AwarenessArticleResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with(['translations', 'newsType']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasPermissionTo('create_awareness_article')),
        ];
    }
}
