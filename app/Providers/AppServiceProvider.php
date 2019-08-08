<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Scheduling\Schedule;
use App\Observers\TaskObserver;
use App\Task;
use App\Events\TaskExecutedEvent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Task::observe(TaskObserver::class);

        if (Schema::hasTable('tasks')) {
            $this->app->resolving(Schedule::class, function ($schedule) {
                $this->schedule($schedule);
            });
        }
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

    public function schedule(Schedule $schedule)
    {
        // Fetch all the active tasks
        $tasks = app('App\Task')->getActive();
        // $tasks = app('App\Task')->where('is_active', true)->get();
        //schedule the tasks
        foreach ($tasks as $task) {
            $event = $schedule->exec($task->command);
            $event->cron($task->expression)
        ->before(function () use ($event) {
            $event->start = microtime(true);
        })
        ->sendOutputTo(storage_path('task-'.sha1($task->command . $task->expression)))
        ->after(function () use ($event,$task) {
            $elapsed_time = microtime(true) - $event->start;
            event(new TaskExecutedEvent($task,$elapsed_time));
        });

            if ($task->dont_overlap) {
                $event->withoutOverlapping();
            }

            if ($task->run_in_maintenance){
                $event->evenInMaintenanceMode();
            }
        }
    }
}
