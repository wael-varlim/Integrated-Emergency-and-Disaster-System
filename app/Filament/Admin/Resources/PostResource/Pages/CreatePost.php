<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource\PostResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use App\Models\News;
use App\Models\Notification;
use App\Models\Post;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function handleRecordCreation(array $data): Post
    {
        // Get the user's address from their known_user record
        $knownUser = Filament::auth()->user()->knownUser;
        
        if (!$knownUser || !$knownUser->address_id) {
            throw new \Exception('User must have an address to create a post.');
        }

        $news = News::create([
            'body'           => $data['news_body'],
            'address_id'     => $knownUser->address_id,
            'known_user_id'  => $knownUser->id,
        ]);

        $post = Post::create([
            'title'    => $data['title'],
            'news_id'  => $news->id,
            'by_admin' => true, // Always true since created by admin
        ]);

        // Always create notification (it's mandatory now)
        Notification::create([
            'title'     => $data['notification_title'],
            'body'      => $data['notification_body'],
            'region_id' => $data['region_id'],
            'post_id'   => $post->id,
        ]);

        return $post;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}