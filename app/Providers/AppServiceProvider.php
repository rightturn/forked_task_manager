<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Scheduling\Schedule;
use App\Observers\TaskObserver;
use App\Task;
use App\Events\TaskExecutedEvent;
use Illuminate\Support\Facades\Log;

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
        // schedule the tasks
        foreach ($tasks as $task) {

            $event = $schedule->exec($task->command); // ->exec('node /home/forge/script.js') Scheduling Shell Commands
            $event->cron($task->expression) // '* * * * *' Run the task on a custom Cron schedule
                ->before(function () use ($event) {
                        $event->start = microtime(true);
                  })
                ->after(function () use ($event,$task) {
                      $elapsed_time = microtime(true) - $event->start;
                      event(new TaskExecutedEvent($task,$elapsed_time));
                  })
                ->sendOutputTo(storage_path('task-'.sha1($task->command . $task->expression))); // send output of the task to specified folder

            if ($task->dont_overlap) {
                /*
                    By default, scheduled tasks will be run even if the previous instance of the task is still running. To prevent this, you may use the withoutOverlapping method:
                */
                $event->withoutOverlapping();
            }

            if ($task->run_in_maintenance){
                $event->evenInMaintenanceMode();
            }
        }
    }
}
