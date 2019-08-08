<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Scheduling\Schedule;

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

        if (Schema::hasTable('tasks')) {
            $this->app->resolving(Schedule::class, function ($schedule) {
                // Fetch all the active tasks
                $tasks = app('App\Task')->where('is_active', true)->get();
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
                    if (file_exists(storage_path('task-'.sha1($task->command . $task->expression)))) {
                        $output = file_get_contents(storage_path('task-'.sha1($task->command . $task->expression)));
                        $task->results()->create([]);
                    }
                });
                }
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
}
