<?php

namespace App\Listeners;

use App\Events\ModelDelete;
use App\Models\RecordHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ModelDeleteListener
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
    public function handle(ModelDelete $event): void
    {
        RecordHistory::createHistory($event->model, $event->getData(), true);
    }
}
