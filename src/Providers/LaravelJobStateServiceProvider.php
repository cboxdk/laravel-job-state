<?php

namespace Cboxdk\LaravelJobState\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelJobStateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->mergeConfigFrom(__DIR__ . '/../config/job-status.php', 'job-status');
        $this->publishes([
            __DIR__ . '../database/migrations/' => database_path('migrations'),
        ], 'migrations');
        $this->publishes([
            __DIR__ . '/../config/' => config_path(),
        ], 'config');
        //dd('ee');
        //$this->bootListeners();
    }

    private function bootListeners()
    {
        /** @var EventManager $eventManager */
        $eventManager = app(config('job-state.event_manager'));
        // Add Event listeners
        app(QueueManager::class)->before(function (JobProcessing $event) use ($eventManager) {
            $eventManager->before($event);
        });
        app(QueueManager::class)->after(function (JobProcessed $event) use ($eventManager) {
            $eventManager->after($event);
        });
        app(QueueManager::class)->failing(function (JobFailed $event) use ($eventManager) {
            $eventManager->failing($event);
        });
        app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) use ($eventManager) {
            $eventManager->exceptionOccurred($event);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
