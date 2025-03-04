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
        // Get current date and time
        $now = Carbon::now('Asia/Manila');
        $today = $now->toDateString(); // YYYY-MM-DD format
        $currentTime = $now->format('H:i:s'); // HH:MM:SS format

        // Get activities that should be marked as missing
        $activities = Activity::where('is_missing', false)
            ->where('is_submitted', false)
            ->where(function ($query) use ($today, $currentTime) {
                $query->where('due_date', '<', $today) // Past due date
                      ->orWhere(function ($q) use ($today, $currentTime) {
                          $q->where('due_date', '=', $today) // Due today
                            ->whereNotNull('due_time')
                            ->where('due_time', '<', $currentTime); // Past due time
                      });
            })
            ->get();

        // Log affected activities before updating
        if ($activities->isEmpty()) {
            Log::info('No activities found to update as missing.');
            $this->info('No activities to update.');
            return;
        }

        foreach ($activities as $activity) {
            Log::info("Marking activity ID {$activity->id} as missing. Due: {$activity->due_date} {$activity->due_time}");
        }

        // Update activities to mark as missing
        $affectedRows = Activity::whereIn('id', $activities->pluck('id'))->update(['is_missing' => true]);

        $this->info("Updated $affectedRows activities as missing.");
    }
}
