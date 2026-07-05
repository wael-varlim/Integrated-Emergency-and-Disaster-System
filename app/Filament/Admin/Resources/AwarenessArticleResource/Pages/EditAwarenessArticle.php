<?php

namespace App\Filament\Admin\Resources\AwarenessArticleResource\Pages;

use App\Filament\Admin\Resources\AwarenessArticleResource\AwarenessArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAwarenessArticle extends EditRecord
{
    protected static string $resource = AwarenessArticleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure icon_url is properly formatted for FileUpload component
        // FileUpload uses Storage::url() which depends on APP_URL
        // We don't need to transform it here - Filament handles it
        // But we verify the file exists
        if (isset($data['icon_url'])) {
            // Check if file exists in storage
            if (!\Storage::disk('public')->exists($data['icon_url'])) {
                // If file doesn't exist, set to null so FileUpload shows empty
                $data['icon_url'] = null;
            }
        }
        
        return $data;
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
