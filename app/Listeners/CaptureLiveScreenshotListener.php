<?php

namespace App\Listeners;

use App\Events\CaptureLiveScreenshot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CaptureLiveScreenshotListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CaptureLiveScreenshot $event): void
    {
//        Log::info('Listener received event', [
//            'userId' => $event->userId,
//            'incident_id' => $event->incident->id,
//        ]);
    }
}
