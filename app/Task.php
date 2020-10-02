<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cron\CronExpression;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;



class Task extends Model
{
    use Notifiable;

    protected $fillable = [
        'description',
        'command',
        'expression',
        'dont_overlap',
        'run_in_maintenance',
        'notification_email'
    ];

    // last_run

    public function getLastRunAttribute()
    {
        if($last = $this->results()->orderBy('id','desc')->first()){
            return $last->ran_at->format("Y-m-d h:i A");
        }
        return 'N/A';
    }

    public function getAverageRuntimeAttribute(){
        return number_format($this->results()->avg('duration') / 1000,2);
    }

    public function getNextRunAttribute()
    {
        return CronExpression::factory($this->getCronExpression())->getNextRunDate('now',0,false,'Asia/Karachi')->format('Y-m-d h:i:t A');
    }

    public function getCronExpression()
    {
        return $this->expression ?: '* * * * *';
    }

    public function results(){
        return $this->hasMany(Result::class);
    }

    public function getActive()
    {
        return $this->getAll()->filter(function($task){
            return $task->is_active;
        });
        // return Cache::rememberForever('tasks.active',function(){
        // });
    }

    public function getAll(){
        return $this->paginate(10);
        // return Cache::rememberForever('tasks.all',function(){
        // });
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        Log::alert("routeNotificationForMail");

        return $this->notification_email;
    }
}
