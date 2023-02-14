<?php

namespace fuma\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Log queue failures
        Queue::failing(function (JobFailed $event) {
            Log :: error('Queue failed', array_combine(['name', 'job', 'exception'], [
                $event->connectionName,
                $event->job->getRawBody(),
                $event->exception->getMessage(),
            ]));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
