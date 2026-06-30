<?php

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Models\NewsType;
use App\Services\PostService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CreatePostFromReport
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected PostService $postService
    ){}

    /**
     * Handle the event.
     */
    public function handle(ReportCreated $event): void
    {

        if ($event->isDirectPost || $event->isPublic === true) {
            $this->postService->createFromReport($event->report);
        }
    }

}
