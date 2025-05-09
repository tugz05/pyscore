<?php

namespace App\Http\Controllers;

use App\Jobs\SendActivityNotification;
use App\Mail\NewActivityNotification;
use App\Models\Activity;
use App\Models\Classlist;
use App\Models\JoinedClass;
use App\Models\Output;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class ClassController extends Controller
{

    // public function getSubmissionStatus($userId, $activityId)
    // {
    //     // Fetch the activity details in one query
    //     $activity = Activity::find($activityId);

    //     // Ensure the activity exists
    //     if (!$activity) {
    //         return response()->json(['error' => 'Activity not found'], 404);
    //     }

    //     // Get the current timestamp
    //     $now = Carbon::now('Asia/Manila');

    //     // Check if the user has submitted the activity
    //     $submission = Activity::where('user_id', $userId)
    //         ->where('id', $activityId)
    //         ->where('is_submitted', true)
    //         ->first();

    //     // Check if the activity is marked as submitted (is_submitted)
    //     $isSubmitted = $submission ? $submission->is_submitted : false;

    //     // Get total score from the activity
    //     $total_score = $activity->points;

    //     // Get assigned score if submission exists, otherwise 0
    //     $assigned_score = $submission ? $submission->score : 0;

    //     // Determine submission status:
    //     if ($activity->is_submitted == true) {
    //         $status = 'Submitted'; // User has submitted
    //     } elseif ($activity->is_missing == true) {
    //         $status = 'Missing'; // Due date has passed, no submission
    //     } else {
    //         $status = 'Pending'; // Due date not yet passed, submission still possible
    //     }

    //     return response()->json([
    //         'submission_status' => $isSubmitted, // Returns true if submitted
    //         'assigned_score' => $assigned_score,
    //         'total_score' => $total_score,
    //         'status' => $status
    //     ]);
    // }

    public function getSubmissionStatus($userId, $activityId)
    {
        // Fetch the activity
        $activity = Activity::find($activityId);

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        // Get current time
        $now = Carbon::now('Asia/Manila');

        // Try to find a submission in the Output table
        $output = Output::where('user_id', $userId)
            ->where('activity_id', $activityId)
            ->first();

        // Determine status based on Output table
        if ($output) {
            $status = match ($output->status) {
                'submitted' => 'Submitted',
                'missing'   => 'Missing',
                default     => 'Pending'
            };
            $assigned_score = $output->score ?? 0;
        } else {
            $status = 'Pending';
            $assigned_score = 0;
        }

        return response()->json([
            'submission_status' => $output?->status ?? null,
            'assigned_score' => $assigned_score,
            'total_score' => $activity->points,
            'status' => $status
        ]);
    }
    public function index()
    {
        return view('instructor.pages.class');
    }
    public function viewStudentsAndTeacher($classlist_id)
    {
        // Fetch the teacher assigned to the class (modify this as needed based on your DB structure)
        $instructor = User::whereHas('classlist', function ($query) use ($classlist_id) {
            $query->where('id', $classlist_id)->where('account_type', 'instructor');
        })->first();

        // Fetch students who have joined the class and are not deleted
        $students = DB::table('joined_classes')
            ->join('users', 'joined_classes.user_id', '=', 'users.id')
            ->where('joined_classes.classlist_id', $classlist_id)
            ->whereNull('joined_classes.deleted_at') // Ensures only active students
            ->select('users.id', 'users.name', 'users.avatar', 'users.email')
            ->get();

        return view('instructor.pages.people', compact('instructor', 'students'));
    }

    public function viewActivity($id)
    {
        $activity = Activity::with('user')->find($id);

        if (!$activity) {
            return abort(404, 'Activity not found.');
        }

        $classlist = Classlist::with('user')->find($activity->classlist_id);

        // Get all enrolled students
        $students = JoinedClass::where('classlist_id', $activity->classlist_id)
            ->with('user')
            ->get();

        // Summary counters
        $summary = [
            'Submitted' => 0,
            'Pending' => 0,
            'Missing' => 0
        ];

        // Process each student's output
        foreach ($students as $student) {
            $output = Output::where('user_id', $student->user->id)
                ->where('activity_id', $activity->id)
                ->first();

            $student->score = $output?->score ?? '--';

            if (!$output) {
                $student->status = 'Pending';
                $summary['Pending']++;
            } elseif ($output->status === 'submitted') {
                $student->status = 'Submitted';
                $summary['Submitted']++;
            } elseif ($output->status === 'missing') {
                $student->status = 'Missing';
                $summary['Missing']++;
            } else {
                $student->status = 'Pending';
                $summary['Pending']++;
            }
        }

        // Sort students by numeric score (desc)
        $students = $students->sortByDesc(function ($student) {
            return is_numeric($student->score) ? (float)$student->score : -1;
        })->values();

        return view('instructor.pages.activity', compact('activity', 'students', 'summary'));
    }



    // Add this method to ClassController.php
    public function getStudentList($activityId)
    {
        $activity = Activity::findOrFail($activityId);

        $students = JoinedClass::where('classlist_id', $activity->classlist_id)
            ->where('is_remove', false)
            ->with('user')
            ->get();

        $summary = [
            'Submitted' => 0,
            'Pending' => 0,
            'Missing' => 0
        ];

        foreach ($students as $student) {
            $output = Output::where('user_id', $student->user->id)
                ->where('activity_id', $activityId)
                ->first();

            // Assign score or placeholder
            $student->score = $output?->score ?? '--';

            // Determine status from Output
            if (!$output) {
                $summary['Pending']++;
                $student->status = 'Pending';
            } else {
                $status = strtolower($output->status);
                if ($status === 'submitted') {
                    $summary['Submitted']++;
                    $student->status = 'Submitted';
                } elseif ($status === 'missing') {
                    $summary['Missing']++;
                    $student->status = 'Missing';
                } else {
                    $summary['Pending']++;
                    $student->status = 'Pending';
                }
            }
        }

        // Sort by score (numeric only)
        $students = $students->sortByDesc(function ($student) {
            return is_numeric($student->score) ? (float)$student->score : -1;
        })->values();

        return response()->json([
            'students' => $students,
            'activity' => $activity,
            'summary' => $summary
        ]);
    }


    public function getStudentOutput($userId, $activityId)
    {
        $output = Output::where('user_id', $userId)
            ->where('activity_id', $activityId)
            ->first();

        if ($output) {
            return response()->json([
                'success' => true,
                'output' => [
                    'code' => $output->code,
                    'score' => $output->score,
                    'feedback' => $output->feedback,
                    'time_consumed' => $output->time_consumed,
                ]
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'No output found']);
        }
    }
    public function getAllClasses($excludeClassId)
    {
        $classes = Classlist::where('is_archive', false)
            ->where('user_id', auth()->user()->id)
            ->where('id', '!=', $excludeClassId) // Exclude the currently opened class
            ->with(['section', 'user']) // Load relationships
            ->orderBy('created_at', 'desc') // Sort by latest time
            ->get();

        return response()->json($classes);
    }


    public function viewClass($id)
    {
        $activities = Activity::where('classlist_id', $id)
            ->with(['classlist', 'section', 'user']) // Load relationships
            ->orderBy('created_at', 'desc') // Sort by latest time
            ->get();

        // Fetch the classlist details separately
        $classlist = Classlist::with('section', 'user')->where('id', $id)->first();
        $instructor = User::whereHas('classlists', function ($query) use ($id) {
            $query->where('id', $id)->where('account_type', 'instructor');
        })->first();

        // Fetch students who have joined the class and are not deleted
        $students = DB::table('joined_classes')
            ->join('users', 'joined_classes.user_id', '=', 'users.id')
            ->where('joined_classes.classlist_id', $id)
            ->where('joined_classes.is_remove', false) // Ensures only active students
            ->whereNull('joined_classes.deleted_at') // Ensures only active students
            ->select('users.id', 'users.name', 'users.avatar', 'users.email')
            ->get();
        return view('instructor.pages.class', compact('activities', 'classlist', 'instructor', 'students'));
    }
    public function list($id)
    {
        $classlist = Classlist::with(['section', 'user'])->find($id);
        $activities = Activity::where('classlist_id', $id)
            ->with(['classlist', 'section', 'user']) // Load relationships
            ->orderBy('created_at', 'desc') // Sort by latest time
            ->get();
        // return response()->json(["data" => $activities]);
        return response()->json([
            'data' => $activities,
            'classlist' => $classlist
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'instruction' => 'nullable|string',
            'points' => 'required|integer|min:0',
            'due_date' => 'required|date',
            'due_time' => 'required',
            'user_id' => 'required|exists:users,id',
            'classlist_id' => 'required|exists:classlists,id',
            'section_id' => 'required|exists:sections,id',
            'selected_classes' => 'nullable|array',
            'selected_classes.*' => 'exists:classlists,id'
        ]);

        // Create main activity
        $activity = Activity::create([
            'user_id' => $request->user_id,
            'classlist_id' => $request->classlist_id,
            'section_id' => $request->section_id,
            'title' => $request->title,
            'instruction' => $request->instruction,
            'points' => $request->points,
            'due_date' => $request->due_date,
            'due_time' => $request->due_time,
            'accessible_date' => $request->accessible_date ?? null,
            'accessible_time' => $request->accessible_time ?? null
        ]);

        // Queue email notification for primary class
        SendActivityNotification::dispatch($activity, $request->classlist_id);

        // Handle shared activity to other classes
        if ($request->has('share_activity') && $request->selected_classes) {
            foreach ($request->selected_classes as $classId) {
                $sharedActivity = Activity::create([
                    'user_id' => $request->user_id,
                    'section_id' => $request->section_id,
                    'classlist_id' => $classId,
                    'title' => $request->title,
                    'instruction' => $request->instruction,
                    'points' => $request->points,
                    'due_date' => $request->due_date,
                    'due_time' => $request->due_time,
                    'accessible_date' => $request->accessible_date ?? null,
                    'accessible_time' => $request->accessible_time ?? null
                ]);

                SendActivityNotification::dispatch($sharedActivity, $classId);
            }
        }

        return response()->json([
            'message' => 'Activity added successfully!',
            'activity' => $activity
        ]);
    }


    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->update($request->all());
        return response()->json(['message' => 'Activity updated successfully!', 'activity' => $activity]);
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();
        return response()->json(['message' => 'Activity deleted successfully!']);
    }
    public function removeStudent(Request $request)
    {
        try {
            // Find the class based on id and user_id
            $class = JoinedClass::where('classlist_id', $request->userID)
                ->where('user_id', $request->id)
                ->firstOrFail();

            // Update the is_remove field
            $class->is_remove = 1;
            $class->save();

            return response()->json(['success' => true, 'message' => 'Removed student successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove student',
                'error' => $e->getMessage() // Optional: for debugging purposes
            ]);
        }
    }



    public function compareStudentOutputs($activityId)
    {
        // Get the class ID for this activity
        $activity = Activity::findOrFail($activityId);
        $classId = $activity->classlist_id;

        // Fetch all student outputs for the given activity, excluding removed students
        $outputs = DB::table('outputs')
            ->join('users', 'outputs.user_id', '=', 'users.id')
            ->join('joined_classes', function ($join) use ($classId) {
                $join->on('outputs.user_id', '=', 'joined_classes.user_id')
                    ->where('joined_classes.classlist_id', $classId)
                    ->where('joined_classes.is_remove', 0); // Only include non-removed students
            })
            ->where('outputs.activity_id', $activityId)
            ->select('outputs.*', 'users.name as student_name', 'users.id as user_id')
            ->get()
            ->unique('user_id'); // Ensure each student appears only once

        // Store normalized codes for comparison
        $normalizedOutputs = [];

        foreach ($outputs as $output) {
            // Normalize code by removing comments, spaces, and making it lowercase
            $cleanCode = $this->normalizeCode($output->code);
            // Skip completely empty code after normalization
            if (trim($cleanCode) === '') {
                continue;
            }
            // If this version of code isn't stored yet, initialize it
            if (!isset($normalizedOutputs[$cleanCode])) {
                $normalizedOutputs[$cleanCode] = [
                    'original_code' => $output->code, // Keep original format for display
                    'students' => []
                ];
            }

            // Store student details with original submitted code
            $normalizedOutputs[$cleanCode]['students'][] = [
                'name' => $output->student_name,
                'full_code' => $output->code
            ];
        }

        // Filter out codes that are submitted by only one student
        $filteredOutputs = array_filter($normalizedOutputs, function ($entry) {
            return count($entry['students']) > 1; // Only keep duplicates
        });

        // Return grouped results as JSON for the frontend
        return response()->json(array_values($filteredOutputs));
    }
    /**
     * Normalize submitted code to detect similar submissions.
     *
     * Steps:
     * 1. Remove all comments (`# this is a comment`).
     * 2. Convert to lowercase to ignore case differences.
     * 3. Trim extra spaces and empty lines.
     * 4. Remove ALL spaces within each line (`print('Sample ')` -> `print('Sample')`).
     */
    private function normalizeCode($code)
    {
        // Remove single-line comments (everything after #)
        $code = preg_replace('/#.*$/m', '', $code);

        // Convert to lowercase (ignores case differences)
        $code = strtolower($code);

        // Remove empty lines and trim spaces
        $codeLines = array_filter(array_map('trim', explode("\n", $code)));

        // Remove ALL spaces within each line (fixes "print('Sample ')" issue)
        $codeLines = array_map(fn($line) => preg_replace('/\s+/', '', $line), $codeLines);

        // Join the lines back into a single string
        return implode("\n", $codeLines);
    }

    public function getSummaryReport($classId)
{
    // Get students in the class
    $students = JoinedClass::where('classlist_id', $classId)
        ->where('is_remove', false)
        ->with('user')
        ->get()
        ->pluck('user'); // Get only user objects

    // Get activities in the class
    $activities = Activity::where('classlist_id', $classId)->get();

    // Prepare report data
    $report = [];

    foreach ($students as $student) {
        $studentData = [
            'name' => $student->name,
            'activities' => []
        ];

        foreach ($activities as $activity) {
            $output = Output::where('user_id', $student->id)
                ->where('activity_id', $activity->id)
                ->first();

            $studentData['activities'][] = [
                'title' => $activity->title,
                'score' => $output->score ?? '--'
            ];
        }

        $report[] = $studentData;
    }

    return response()->json($report);
}
public function downloadScores($id)
{
    $activity = Activity::with('user', 'classlist')->findOrFail($id);

    $userIds = JoinedClass::where('classlist_id', $activity->classlist_id)
        ->where('is_remove', false)
        ->pluck('user_id');

    $outputs = Output::whereIn('user_id', $userIds)
        ->where('activity_id', $id)
        ->get()
        ->keyBy('user_id');

    $students = User::whereIn('id', $userIds)->get();

    $submitted = $outputs->where('status', 'submitted')->count();
    $missing = $outputs->where('status', 'missing')->count();
    $pending = $userIds->count() - $submitted - $missing;

    return view('instructor.pages.scores-pdf', [
        'activity' => $activity,
        'students' => $students,
        'outputs' => $outputs,
        'submitted' => $submitted,
        'pending' => $pending,
        'missing' => $missing
    ]);
}

}
