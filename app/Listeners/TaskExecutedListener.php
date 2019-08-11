<?php

namespace App\Listeners;

use App\Events\TaskExecutedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\TaskCompleted;
use Illuminate\Support\Facades\Log;


class TaskExecutedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TaskExecutedEvent  $event
     * @return void
     */
    public function handle(TaskExecutedEvent $event)
    {
        if (file_exists(storage_path('task-'.sha1($event->task->command . $event->task->expression)))) {
            $output = file_get_contents(storage_path('task-'.sha1($event->task->command . $event->task->expression)));
            $event->task->results()->create([
                'duration' => $event->elapsed_time * 1000,
                'result' => $output
            ]);
            unlink(storage_path('task-'.sha1($event->task->command . $event->task->expression)));
        }
        Log::alert("handle method in TaskExecutedListener");
        Log::alert($event->task);

        $event->task->notify(new TaskCompleted($event->task));
        
    }
}
