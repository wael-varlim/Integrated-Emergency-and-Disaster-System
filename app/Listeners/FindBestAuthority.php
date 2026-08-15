<?php

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Services\AuthorityService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FindBestAuthority
{
    /**
     * Create the event listener.
     */
    public function __construct(private AuthorityService $authority_service)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReportCreated $event): void
    {
        //
    }
}
