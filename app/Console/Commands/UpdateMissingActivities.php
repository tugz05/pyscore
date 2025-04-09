<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\JoinedClass;
use App\Models\Output;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMissingActivities extends Command
{
    protected $signature = 'activities:update-missing';
    protected $description = 'Mark outputs as missing for students who did not submit past the deadline';

    public function handle()
    {
        $now = Carbon::now('Asia/Manila');

        // Fetch all activities that are past due
        $activities = Activity::whereDate('due_date', '<=', $now->toDateString())
            ->whereTime('due_time', '<=', $now->toTimeString())
            ->get();

        foreach ($activities as $activity) {
            if (!$activity->classlist_id) {
                Log::warning("Skipping activity ID {$activity->id} due to missing classlist_id.");
                continue;
            }

            // Get all students enrolled in this activity's class
            $students = JoinedClass::where('classlist_id', $activity->classlist_id)
                ->get();

            foreach ($students as $joined) {
                $userId = $joined->user_id;
                // Check if student already has an output
                $existingOutput = Output::where('user_id', $userId)
                    ->where('activity_id', $activity->id)
                    ->first();

                if (!$existingOutput) {
                    Output::create([
                        'user_id' => $userId,
                        'activity_id' => $activity->id,
                        'section_id' => $activity->section_id,
                        'code' => '',
                        'feedback' => '',
                        'created_at' => $now,
                        'status' => 'missing',
                        'score' => 0,
                    ]);

                    Log::info("✅ Marked missing: Activity ID {$activity->id}, User ID {$userId}");
                }
            }
        }

        $this->info('✅ All missing submissions have been recorded in the Output table.');
    }
}
