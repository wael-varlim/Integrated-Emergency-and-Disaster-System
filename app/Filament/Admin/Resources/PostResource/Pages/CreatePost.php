<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Address;
use App\Models\News;
use App\Models\Notification;
use App\Models\Post;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function handleRecordCreation(array $data): Post
    {
        $address = Address::create([
            'street'  => $data['street'],
            'city_id' => $data['city_id'],
        ]);

        $news = News::create([
            'body'       => $data['news_body'],
            'address_id' => $address->id,
            'user_id'    => Filament::auth()->id(),
        ]);

        $post = Post::create([
            'title'      => $data['title'],
            'owner_role' => $data['owner_role'],
            'news_id'    => $news->id,
        ]);

        if (($data['create_notification'] ?? false) && !empty($data['notification_title'])) {
            Notification::create([
                'title'     => $data['notification_title'],
                'body'      => $data['notification_body'],
                'region_id' => $data['region_id'],
                'post_id'   => $post->id,
            ]);
        }

        return $post;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}