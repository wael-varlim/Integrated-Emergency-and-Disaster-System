<?php

namespace App\Filament\Admin\Widgets;

use App\Models\NewsType;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class NewsByTypeChart extends ChartWidget
{
    protected ?string $heading = 'News by Type';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $results = NewsType::query()
            ->join('news_types_news', 'news_types_news.news_type_id', '=', 'news_types.id')
            ->select('news_types.type_name', DB::raw('COUNT(news_types_news.news_id) as total'))
            ->groupBy('news_types.id', 'news_types.type_name')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'News',
                    'data' => $results->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                ],
            ],
            'labels' => $results->pluck('type_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
