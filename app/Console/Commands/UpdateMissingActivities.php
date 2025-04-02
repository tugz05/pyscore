<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMissingActivities extends Command
{
    protected $signature = 'activities:update-missing';
    protected $description = 'Mark activities as missing if due_date and due_time are past due';

    public function handle()
    {
        $now = Carbon::now('Asia/Manila'); // Get the current timestamp

        // Get overdue activities
        $activities = Activity::where('is_missing', 0)
            ->where('is_submitted', 0)
            ->where('due_date', '<=', $now->toDateString())
            ->where('due_time', '<=', $now->toTimeString())
            ->get();
        foreach ($activities as $activity) {
            $dueDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $activity->due_date . ' ' . $activity->due_time, 'Asia/Manila');
            if ($now->greaterThan($dueDateTime)) {
                // Update the activity status to 'missing'
                $activity->is_missing = 1;
                $activity->save();
                Log::info("Activity ID {$activity->id} marked as missing.");
            }
        }
        $this->info($activities);


    }
}
