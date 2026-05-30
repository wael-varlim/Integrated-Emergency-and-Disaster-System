<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Report;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReportsByMonthChart extends ChartWidget
{
    protected ?string $heading = 'Reports by Month';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $results = Report::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(id) as total'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = $results->pluck('month')->toArray();
        $totals = $results->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Reports',
                    'data' => $totals,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
