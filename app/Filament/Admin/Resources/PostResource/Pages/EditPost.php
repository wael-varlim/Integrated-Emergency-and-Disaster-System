<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;
use App\Filament\Admin\Resources\PostResource;
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
        $news = News::find($data['news_id']);
        if ($news) {
            $data['news_body'] = $news->body;
            if ($news->address) {
                $data['city_id'] = $news->address->city_id;
                $data['street'] = $news->address->street;
            }
        }

        $notification = Notification::find($data['notification_id']);
        if ($notification) {
            $data['create_notification'] = true;
            $data['notification_title'] = $notification->title;
            $data['notification_body'] = $notification->body;
            $data['region_id'] = $notification->region_id;
        }


        return $data;
    }
}