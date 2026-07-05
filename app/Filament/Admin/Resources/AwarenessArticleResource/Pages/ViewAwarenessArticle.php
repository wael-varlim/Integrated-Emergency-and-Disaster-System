<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Pages;

use App\Filament\Admin\Resources\AwarenessArticleResource\AwarenessArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAwarenessArticle extends ViewRecord
{
    protected static string $resource = AwarenessArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->hasPermissionTo('update_awareness_article')),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasPermissionTo('delete_awareness_article')),
        ];
    }
}
