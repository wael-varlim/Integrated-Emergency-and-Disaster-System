<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendPostNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private NotificationService $notificationService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $post = $event->post->loadMissing(['news.newsType.arabicTranslation', 'news.address.city.governorate']);

        Log::info('PostCreated listener fired', ['post_id' => $post->id, 'by_admin' => $post->by_admin]);

        $this->notifyEveryone($post);

        //region based filter ===for later===
        // if ($post->by_admin) {
        //     $this->notifyEveryone($post);
        // } else {
        //     $this->notifyByRegion($post);
        // }
    }


    private function notifyEveryone($post): void
    {
        User::whereIn('user_type', ['Known user'])
            ->with('fcmTokens')
            ->chunkById(200, function ($users) use ($post) {
                foreach ($this->groupByLanguage($users) as $lang => $usersInLang) {
                    $tokens = $usersInLang->pluck('fcmTokens')->flatten()->pluck('token');
                    $this->sendBatched($tokens, $post, $lang);
                }
            });
    }
//=========for later=========
    // private function notifyByRegion($post): void
    // {
    //     $regionIds = $this->resolveRegionIds($post);

    //     if (empty($regionIds)) {
    //         return;
    //     }

    //     User::whereHas('regions', fn ($q) => $q->whereIn('regions.id', $regionIds))
    //         ->with('fcmTokens')
    //         ->chunkById(200, function ($users) use ($post) {
    //             foreach ($this->groupByLanguage($users) as $lang => $usersInLang) {
    //                 $tokens = $usersInLang->pluck('fcmTokens')->flatten()->pluck('token');
    //                 $this->sendBatched($tokens, $post, $lang);
    //             }
    //         });
    // }

    // private function resolveRegionIds($post): array
    // {
    //     $address = $post->news?->report?->address;

    //     if (!$address) {
    //         return [];
    //     }

    //     $regionIds = [];

    //     if ($address->city) {
    //         $regionIds[] = $address->city->region_id;
    //         if ($address->city->governorate) {
    //             $regionIds[] = $address->city->governorate->region_id;
    //         }
    //     }

    //     return array_values(array_unique($regionIds));
    // }

    private function groupByLanguage($users)
    {
        return $users->groupBy(fn ($user) => $user->notification_lang ?? 'ar');
    }

    private function sendBatched($tokens, $post, string $lang): void
    {
        $tokens = $tokens->filter()->unique()->values();

        if ($tokens->isEmpty()) {
            return;
        }

        [$title, $body] = $this->buildContent($post, $lang);
        $data = $this->buildNotificationData($post);

        foreach ($tokens->chunk(500) as $chunk) {
            $this->notificationService->sendToTokens($chunk->toArray(), $title, $body, $data);
        }
    }

    private function buildContent($post, string $lang): array
    {
        $report = $post->news?->report;
        $types = $post->news?->newsType ?? collect();

        $title = $types
            ->map(function ($type) use ($lang) {
                if ($lang === 'en') {
                    return $type->type_name;
                }

                return $type->arabicTranslation?->translation ?? $type->type_name;
            })
            ->implode(', ');

        $address = $report?->news?->address;

        $body = collect([
            $address?->city?->governorate?->name,
            $address?->city?->name,
            $address?->street,
        ])->filter()->implode(', ');

        return [$title, $body];
    }

    private function buildNotificationData($post): array
    {
        $typeCodes = $post->news?->newsType?->pluck('slug')->filter()->values()->toArray() ?? [];

        return [
            'post_id' => (string) $post->id,
            'type_codes' => implode(',', $typeCodes),
        ];
    }
}
