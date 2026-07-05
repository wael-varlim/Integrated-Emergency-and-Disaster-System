<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Pages;

use App\Filament\Admin\Resources\AwarenessArticleResource\AwarenessArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAwarenessArticle extends CreateRecord
{
    protected static string $resource = AwarenessArticleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
