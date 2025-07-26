<?php

namespace App\View\Components;

use App\Exceptions\UserException;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GoogleCapacha extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $google_cacha_key = env("GOOGLE_RECAPTCHA_SITE_KEY");

        if(!$google_cacha_key)
        {
            throw new UserException("env : GOOGLE_RECAPTCHA_SITE_KEY : not set");
        }

        return view('components.google-capacha', compact("google_cacha_key"));
    }
}
