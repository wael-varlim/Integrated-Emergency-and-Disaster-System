<?php

namespace App\Services;



use App\Http\Controllers\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;

class NotificationService
{
    use ApiResponseTrait;

    public function __construct(private Messaging $messaging) {}

    public function sendToTokens(array $tokens, string $title, string $body, array $data = [], ?string $imageUrl = null): void
    {
        $notification = $imageUrl
            ? FirebaseNotification::create($title, $body, $imageUrl)
            : FirebaseNotification::create($title, $body);

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data)
            ->withAndroidConfig(AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'channel_id' => 'high_importance_channel',
                    'icon' => 'ic_notification',
                    'color' => '#1B4343',
                    'sound' => 'default',
                    'notification_priority' => 'PRIORITY_MAX',
                ],
            ]));

        $report = $this->messaging->sendMulticast($message, $tokens);

        Log::info('FCM send report', [
            'success_count' => $report->successes()->count(),
            'failure_count' => $report->failures()->count(),
        ]);

        foreach ($report->failures()->getItems() as $failure) {
            Log::warning('FCM token failed', [
                'token' => $failure->target()->value(),
                'reason' => $failure->error()?->getMessage(),
            ]);
        }
    }

    public function sendToUser(\App\Models\User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->fcmTokens->pluck('token')->toArray();

        if (empty($tokens)) {
            return;
        }

        $this->sendToTokens($tokens, $title, $body, $data);
    }
    
}