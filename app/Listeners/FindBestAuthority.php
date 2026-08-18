<?php

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Services\AuthorityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FindBestAuthority
{
    /**
     * Create the event listener.
     */
    public function __construct(private AuthorityService $authorityService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReportCreated $event): void
    {
        $report = $event->report->loadMissing('news.newsType');

        //get the coordinates from raw POINT
        $coordinates = DB::table('reports')
            ->selectRaw('ST_Y(location) as latitude, ST_X(location) as longitude')
            ->where('id', $report->id)
            ->first();

        if (!$coordinates || !$coordinates->latitude || !$coordinates->longitude) {
            Log::warning('No coordinates found for report', ['report_id' => $report->id]);
            return;
        }


        $authorityTypes = $this->FindMatchingAuthorityTypes($report);

        $nearestAuthorities = $this->authorityService->findNearestAuthorities(
            $authorityTypes,
            $coordinates->latitude,
            $coordinates->longitude
        );

        if ($nearestAuthorities != null)
        {
            $report->news?->authority()->syncWithoutDetaching(
                $nearestAuthorities->pluck('id')
            );
                
            Log::info('Matched nearest authorities', [
                'report_id' => $report->id,
                'authorities' => $nearestAuthorities->pluck('id', 'authority_type_id'),
            ]);
        }


    }


    private function FindMatchingAuthorityTypes($report)
    {
        $newsTypeIds = $report->news?->newsType->pluck('id')->toArray() ?? [];

        if (empty($newsTypeIds)) {
            Log::warning('No news types found for report', ['report_id' => $report->id]);
            return;
        }

        $authorityTypes = $this->authorityService->FindMatchingAuthorityTypes($newsTypeIds);

        Log::info('Matched authority types', [
            'report_id' => $report->id,
            'authority_types' => $authorityTypes->pluck('type_name'),
        ]);

        return $authorityTypes;
    }
}
