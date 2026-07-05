<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource\PostResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Models\News;
use App\Models\NewsTranslation;
use App\Models\Notification;
use App\Models\NotificationTranslations;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Region;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract nested data from dot notation
        $processedData = [
            'title' => $data['title']['en'] ?? $data['title']['ar'] ?? '',
            'title_ar' => $data['title']['ar'] ?? null,
            'title_en' => $data['title']['en'] ?? null,
            
            'news_body' => $data['news_body']['en'] ?? $data['news_body']['ar'] ?? '',
            'news_body_ar' => $data['news_body']['ar'] ?? null,
            'news_body_en' => $data['news_body']['en'] ?? null,
            
            'notification_title' => $data['notification_title']['en'] ?? $data['notification_title']['ar'] ?? '',
            'notification_title_ar' => $data['notification_title']['ar'] ?? null,
            'notification_title_en' => $data['notification_title']['en'] ?? null,
            
            'notification_body' => $data['notification_body']['en'] ?? $data['notification_body']['ar'] ?? '',
            'notification_body_ar' => $data['notification_body']['ar'] ?? null,
            'notification_body_en' => $data['notification_body']['en'] ?? null,
        ];

        return $processedData;
    }

    protected function handleRecordCreation(array $data): Post
    {
        // Get the user's address from their known_user record
        $knownUser = Filament::auth()->user()->knownUser;
        
        if (!$knownUser) {
            throw new \Exception('User must be a known user to create a post.');
        }

        // Create News (use English as primary)
        $news = News::create([
            'body'           => $data['news_body'],
            'address_id'     => $knownUser->address_id ?? 1,
            'known_user_id'  => $knownUser->id,
        ]);

        // Create News Translations
        if (!empty($data['news_body_ar'])) {
            NewsTranslation::create([
                'language_code' => 'ar',
                'translation'   => $data['news_body_ar'],
                'news_id'       => $news->id,
            ]);
        }
        
        if (!empty($data['news_body_en'])) {
            NewsTranslation::create([
                'language_code' => 'en',
                'translation'   => $data['news_body_en'],
                'news_id'       => $news->id,
            ]);
        }

        // Create Post (use English as primary)
        $post = Post::create([
            'title'    => $data['title'],
            'news_id'  => $news->id,
            'by_admin' => true,
        ]);

        // Create Post Translations
        if (!empty($data['title_ar'])) {
            PostTranslation::create([
                'language_code' => 'ar',
                'translation'   => $data['title_ar'],
                'post_id'       => $post->id,
            ]);
        }
        
        if (!empty($data['title_en'])) {
            PostTranslation::create([
                'language_code' => 'en',
                'translation'   => $data['title_en'],
                'post_id'       => $post->id,
            ]);
        }

        // Get all regions
        $regions = Region::all();

        // Create notifications for ALL regions
        foreach ($regions as $region) {
            $notification = Notification::create([
                'title'     => $data['notification_title'],
                'body'      => $data['notification_body'],
                'region_id' => $region->id,
                'post_id'   => $post->id,
            ]);

            // Create Notification Translations
            if (!empty($data['notification_title_ar']) && !empty($data['notification_body_ar'])) {
                NotificationTranslations::create([
                    'language_code'     => 'ar',
                    'title_translation' => $data['notification_title_ar'],
                    'body_translation'  => $data['notification_body_ar'],
                    'notification_id'   => $notification->id,
                ]);
            }
            
            if (!empty($data['notification_title_en']) && !empty($data['notification_body_en'])) {
                NotificationTranslations::create([
                    'language_code'     => 'en',
                    'title_translation' => $data['notification_title_en'],
                    'body_translation'  => $data['notification_body_en'],
                    'notification_id'   => $notification->id,
                ]);
            }
        }

        return $post;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}