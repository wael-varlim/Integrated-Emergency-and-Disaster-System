<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AwarenessArticle;
use App\Models\News;
use App\Models\User;
use App\Models\Notification;
use App\Models\Report;
use App\Models\Suggestion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Reports', Report::count())
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->description('Total reports submitted'),

            Stat::make('Total Users', User::where('user_type', 'Known user')->count())
                ->icon('heroicon-o-users')
                ->color('info')
                ->description('Registered mobile users'),

            Stat::make('Total News', News::count())
                ->icon('heroicon-o-newspaper')
                ->color('success')
                ->description('Published news articles'),

            Stat::make('Awareness Articles', AwarenessArticle::count())
                ->icon('heroicon-o-light-bulb')
                ->color('warning')
                ->description('Awareness articles'),

            Stat::make('Unread Suggestions', Suggestion::where('is_read_by_admin', false)->count())
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('primary')
                ->description('Awaiting review'),

            Stat::make('Total Notifications', Notification::count())
                ->icon('heroicon-o-bell')
                ->color('gray')
                ->description('Sent notifications'),
        ];
    }
}
