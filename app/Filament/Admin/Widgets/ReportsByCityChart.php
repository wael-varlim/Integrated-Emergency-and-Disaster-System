<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Report;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReportsByCityChart extends ChartWidget
{
    protected ?string $heading = 'Reports by City';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $results = Report::query()
            ->join('news', 'news.id', '=', 'reports.news_id')
            ->join('addresses', 'addresses.id', '=', 'news.address_id')
            ->join('cities', 'cities.id', '=', 'addresses.city_id')
            ->join('governorates', 'governorates.id', '=', 'cities.governorate_id')
            ->where('governorates.name', 'Damascus')
            ->select('cities.name as city_name', DB::raw('COUNT(reports.id) as total'))
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Reports',
                    'data' => $results->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(249, 115, 22, 0.7)',
                        'rgba(234, 179, 8, 0.7)',
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(168, 85, 247, 0.7)',
                        'rgba(236, 72, 153, 0.7)',
                        'rgba(14, 165, 233, 0.7)',
                        'rgba(20, 184, 166, 0.7)',
                        'rgba(132, 204, 22, 0.7)',
                        'rgba(251, 146, 60, 0.7)',
                        'rgba(167, 139, 250, 0.7)',
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(249, 115, 22)',
                        'rgb(234, 179, 8)',
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                        'rgb(14, 165, 233)',
                        'rgb(20, 184, 166)',
                        'rgb(132, 204, 22)',
                        'rgb(251, 146, 60)',
                        'rgb(167, 139, 250)',
                    ],
                ],
            ],
            'labels' => $results->pluck('city_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
