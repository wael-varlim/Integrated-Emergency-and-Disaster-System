<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Pages;

use App\Filament\Admin\Resources\AwarenessArticleResource\AwarenessArticleResource;
use App\Models\AwarenessArticleTranslation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwarenessArticle extends EditRecord
{
    protected static string $resource = AwarenessArticleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load translations and format for the form
        $translations = $this->record->translations;
        
        foreach ($translations as $translation) {
            $data['translations'][$translation->language_code] = [
                'title' => $translation->title,
                'body' => $translation->body,
            ];
        }
        
        // Ensure icon_url is properly formatted
        if (isset($data['icon_url'])) {
            if (!\Storage::disk('public')->exists($data['icon_url'])) {
                $data['icon_url'] = null;
            }
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract translations
        $translations = $data['translations'] ?? [];
        unset($data['translations']);
        
        // Store translations temporarily
        $this->translations = $translations;
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Update translations
        if (isset($this->translations)) {
            foreach ($this->translations as $languageCode => $translation) {
                AwarenessArticleTranslation::updateOrCreate(
                    [
                        'awareness_article_id' => $this->record->id,
                        'language_code' => $languageCode,
                    ],
                    [
                        'title' => $translation['title'] ?? '',
                        'body' => $translation['body'] ?? '',
                    ]
                );
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasPermissionTo('delete_awareness_article')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
