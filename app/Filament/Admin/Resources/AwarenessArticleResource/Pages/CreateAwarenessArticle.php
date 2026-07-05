<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Pages;

use App\Filament\Admin\Resources\AwarenessArticleResource\AwarenessArticleResource;
use App\Models\AwarenessArticleTranslation;
use Filament\Resources\Pages\CreateRecord;

class CreateAwarenessArticle extends CreateRecord
{
    protected static string $resource = AwarenessArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract translations
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        
        // Store translations temporarily
        $this->translations = $translations;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Save translations
        if (isset($this->translations)) {
            foreach ($this->translations as $languageCode => $translation) {
                AwarenessArticleTranslation::create([
                    'awareness_article_id' => $this->record->id,
                    'language_code' => $languageCode,
                    'title' => $translation['title'] ?? '',
                    'body' => $translation['body'] ?? '',
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
