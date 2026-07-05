<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\PostResource;
use App\Models\News;
use App\Models\Notification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Initialize arrays
        $data['title'] = [];
        $data['news_body'] = [];
        $data['notification_title'] = [];
        $data['notification_body'] = [];

        // Load post translations
        $postTranslations = $this->record->postTranslations;
        foreach ($postTranslations as $translation) {
            $data['title'][$translation->language_code] = $translation->translation;
        }

        // Load news and news translations
        $news = News::with('newsTranslations', 'address')->find($data['news_id']);
        if ($news) {
            // Load news body translations
            foreach ($news->newsTranslations as $translation) {
                $data['news_body'][$translation->language_code] = $translation->translation;
            }

            // Load address details
            if ($news->address) {
                $data['city_id'] = $news->address->city_id;
                $data['street'] = $news->address->street;
            }
        }

        // Load notification and notification translations
        $notification = $this->record->notification;
        if ($notification) {
            $data['create_notification'] = true;
            $data['notification_title']['en'] = $notification->title;
            $data['notification_body']['en'] = $notification->body;
            $data['region_id'] = $notification->region_id;

            // Load notification translations
            $notificationTranslations = $notification->notificationTranslations;
            foreach ($notificationTranslations as $translation) {
                $data['notification_title'][$translation->language_code] = $translation->title_translation;
                $data['notification_body'][$translation->language_code] = $translation->body_translation;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract title translations and save to post_translations table
        if (isset($data['title']) && is_array($data['title'])) {
            $titleTranslations = $data['title'];
            
            // Use English title as the main title field
            $data['title'] = $titleTranslations['en'] ?? '';
            
            // Save translations to post_translations
            $this->record->postTranslations()->delete(); // Clear old translations
            foreach ($titleTranslations as $lang => $translation) {
                if (!empty($translation)) {
                    $this->record->postTranslations()->create([
                        'language_code' => $lang,
                        'translation' => $translation,
                    ]);
                }
            }
        }

        // Handle news body translations
        if (isset($data['news_body']) && is_array($data['news_body'])) {
            $newsBodyTranslations = $data['news_body'];
            $news = $this->record->news;
            
            if ($news) {
                // Update main news body with English
                $news->body = $newsBodyTranslations['en'] ?? '';
                $news->save();
                
                // Save translations
                $news->newsTranslations()->delete();
                foreach ($newsBodyTranslations as $lang => $translation) {
                    if (!empty($translation)) {
                        $news->newsTranslations()->create([
                            'language_code' => $lang,
                            'translation' => $translation,
                        ]);
                    }
                }
            }
            
            unset($data['news_body']);
        }

        // Handle notification translations
        if (isset($data['notification_title']) && is_array($data['notification_title'])) {
            $notification = $this->record->notification;
            
            if ($notification) {
                // Update main notification with English
                $notification->title = $data['notification_title']['en'] ?? '';
                $notification->body = $data['notification_body']['en'] ?? '';
                $notification->save();
                
                // Save translations
                $notification->notificationTranslations()->delete();
                foreach ($data['notification_title'] as $lang => $title) {
                    $body = $data['notification_body'][$lang] ?? '';
                    if (!empty($title) && !empty($body)) {
                        $notification->notificationTranslations()->create([
                            'language_code' => $lang,
                            'title_translation' => $title,
                            'body_translation' => $body,
                        ]);
                    }
                }
            }
            
            unset($data['notification_title']);
            unset($data['notification_body']);
        }

        unset($data['city_id']);
        unset($data['street']);
        unset($data['create_notification']);
        unset($data['region_id']);

        return $data;
    }
}
