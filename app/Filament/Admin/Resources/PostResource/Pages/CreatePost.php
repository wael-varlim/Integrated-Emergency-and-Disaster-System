<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Address;
use App\Models\News;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function handleRecordCreation(array $data): Post
    {
        // 1. Create the Address
        $address = Address::create([
            'street'  => $data['street'],
            'city_id' => $data['city_id'],
        ]);

        // 2. Create the News record (user_id = current admin)
        $news = News::create([
            'body'       => $data['news_body'],
            'address_id' => $address->id,
            'user_id'    => Auth::id(),
        ]);

        // 3. Create the Notification if requested
        $notificationId = null;
        if (($data['create_notification'] ?? false) && !empty($data['notification_title'])) {
            $notification = Notification::create([
                'title'     => $data['notification_title'],
                'body'      => $data['notification_body'],
                'region_id' => $data['region_id'],
            ]);
            $notificationId = $notification->id;
        }

        // 4. Create the Post record
        $post = Post::create([
            'title'           => $data['title'],
            'owner_role'      => $data['owner_role'],
            'news_id'         => $news->id,
            'notification_id' => $notificationId,
        ]);

        return $post;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}