<?php

namespace App\Listeners;

use App\Events\ModelInsert;
use App\Models\RecordHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ModelInsertListener implements ShouldQueue
{
     use InteractsWithQueue;
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
    public function handle(ModelInsert $event): void
    {
        RecordHistory::createHistory($event->model, $event->getData());
    }
}
