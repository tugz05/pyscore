<?php

namespace App\Jobs;

use App\Mail\NewActivityNotification;
use App\Models\Activity;
use App\Models\JoinedClass;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendActivityNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $activity;
    protected $classlistId;

    public function __construct(Activity $activity, $classlistId)
    {
        $this->activity = $activity;
        $this->classlistId = $classlistId;
    }

    public function handle()
    {
        $classes = JoinedClass::with('user', 'classlist')
                ->where('classlist_id', $this->classlistId)
                ->get();
            // assuming 'students' is a relationship
            foreach ($classes as $joinedClass) {
                $student = $joinedClass->user;
                if ($student && $student->account_type === 'student') {
                    Mail::to($student->email)->send(new NewActivityNotification($this->activity, $joinedClass->classlist));
                }
            }
    }
}
