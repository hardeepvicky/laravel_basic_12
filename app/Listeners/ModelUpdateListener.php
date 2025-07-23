<?php

namespace App\Listeners;

use App\Events\ModelUpdate;
use App\Models\RecordHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ModelUpdateListener  implements ShouldQueue
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
    public function handle(ModelUpdate $event): void
    {
        RecordHistory::createHistory($event->model, $event->getData());
    }
}
