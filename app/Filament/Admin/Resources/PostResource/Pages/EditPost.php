<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource\PostResource;
use App\Models\News;
use App\Models\Notification;
use App\Models\Media;
use App\Models\MediaType;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Normalize a stored media_url into a bare path relative to the 'public'
     * disk root (e.g. "post-images/xxx.jpg"), regardless of whether it was
     * historically saved as a full URL, "/storage/...", or "storage/...".
     * The FileUpload component only knows how to hydrate/preview bare
     * relative paths — anything else leaves it stuck on "loading...".
     */
    private function normalizeImagePath(string $url): string
    {
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $url = parse_url($url, PHP_URL_PATH) ?? $url;
        }

        $url = ltrim($url, '/');

        if (str_starts_with($url, 'storage/')) {
            $url = substr($url, strlen('storage/'));
        }

        return $url;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Initialize arrays
        $data['title'] = [];
        $data['news_body'] = [];
        $data['notification_title'] = [];
        $data['notification_body'] = [];
        $data['new_images'] = [];

        // Load post translations
        $postTranslations = $this->record->postTranslations;
        foreach ($postTranslations as $translation) {
            $data['title'][$translation->language_code] = $translation->translation;
        }

        // Load news and news translations with media eager loading
        $news = News::with(['newsTranslations', 'address', 'media.mediaType'])->find($data['news_id']);
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

            // Load existing images directly into the same field used for new uploads,
            // so the FileUpload component shows them as already-uploaded files that
            // can be removed (or left alone) alongside adding new ones.
            $imageMediaType = MediaType::where('type_name', 'image')->first();
            if ($imageMediaType) {
                $data['new_images'] = $news->media()
                    ->where('media_type_id', $imageMediaType->id)
                    ->pluck('media_url')
                    ->map(fn ($url) => $this->normalizeImagePath($url))
                    ->filter(fn ($path) => Storage::disk('public')->exists($path))
                    ->values()
                    ->toArray();
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

        // Handle images: the field now contains BOTH existing and newly-uploaded
        // paths together, since the form pre-fills it with existing images.
        // Diff against what's in the DB: anything missing was removed by the
        // user and should be deleted; anything not yet in the DB is a new
        // upload and should be created.
        if (isset($data['new_images']) && is_array($data['new_images'])) {
            $news = $this->record->news;

            if ($news) {
                $imageMediaType = MediaType::firstOrCreate(['type_name' => 'image']);

                $existingMedia = $news->media()
                    ->where('media_type_id', $imageMediaType->id)
                    ->get();

                // Compare using the same normalized form used to fill the form,
                // regardless of how each row happens to be stored in the DB.
                $existingByNormalizedPath = $existingMedia->keyBy(
                    fn ($media) => $this->normalizeImagePath($media->media_url)
                );
                $existingPaths = $existingByNormalizedPath->keys()->toArray();

                $submittedPaths = array_values($data['new_images']);

                // Images removed in the UI: present in DB, absent from submission
                $removedPaths = array_diff($existingPaths, $submittedPaths);
                foreach ($removedPaths as $path) {
                    $media = $existingByNormalizedPath->get($path);
                    if ($media) {
                        Storage::disk('public')->delete($media->media_url);
                        $media->delete();
                    }
                }

                // Newly uploaded images: present in submission, absent from DB
                $newPaths = array_diff($submittedPaths, $existingPaths);
                foreach ($newPaths as $imagePath) {
                    Media::create([
                        'media_url' => $imagePath,
                        'media_type_id' => $imageMediaType->id,
                        'model_type' => News::class,
                        'model_id' => $news->id,
                    ]);
                }
            }

            unset($data['new_images']);
        }

        unset($data['city_id']);
        unset($data['street']);
        unset($data['create_notification']);
        unset($data['region_id']);

        return $data;
    }
}