<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Pages;

use App\Filament\Admin\Resources\AwarenessArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAwarenessArticles extends ManageRecords
{
    protected static string $resource = AwarenessArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasPermissionTo('create_awareness_article')),
        ];
    }
}