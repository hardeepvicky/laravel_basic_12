<?php

namespace App\View\Components\Backend;

use App\Helpers\DateUtility;
use App\View\Components\BackendComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CelecDateTime extends BackendComponent
{
    /**
     * Create a new component instance.
     */
    public function __construct(
            public $label = "",
            public $by = "",
            public $value = "",
            public $utcDate = "",
            public $timezone = "",
    )
    {
        //

        if ($this->utcDate && $this->timezone)
        {
            if (is_object($this->utcDate))
            {
                $dt = clone($this->utcDate);
            }
            else
            {
                $dt = DateUtility::getDateObj($this->utcDate);
            }

            $dt->setTimezone(new \DateTimeZone($this->timezone));

            $secondDate['date'] = $dt->format(DateUtility::DATE_OUT_FORMAT);
            $secondDate['time'] = $dt->format(DateUtility::TIME_OUT_FORMAT);
            $secondDate['day'] = $dt->format('D');

            $this->setForView(compact("secondDate"));
        }

        $this->view_path = "celec-date-time";
    }
}
