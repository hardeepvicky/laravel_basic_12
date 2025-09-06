<?php

namespace App\Providers;

use App\Helpers\LaravelExtend;
use App\Models\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path('define_constants.php');
        require_once app_path('basic_functions.php');

        $this->app->bind(DatabaseNotification::class, Notification::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!App::environment('production')) {
            DB::enableQueryLog();

            if (LaravelExtend::check_database_connection("mongodb"))
            {
                DB::connection('mongodb')->enableQueryLog();
            }
        }
    }
}
