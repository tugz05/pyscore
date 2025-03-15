<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classlist;
use App\Models\JoinedClass;
use App\Models\Output;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{

    public function getSubmissionStatus($userId, $activityId)
    {
        // Fetch the activity details in one query
        $activity = Activity::find($activityId);

        // Ensure the activity exists
        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        // Get the current timestamp
        $now = Carbon::now('Asia/Manila');

        // Check if the user has submitted the activity
        $submission = Activity::where('user_id', $userId)
            ->where('id', $activityId)
            ->where('is_submitted', true)
            ->first();

        // Check if the activity is marked as submitted (is_submitted)
        $isSubmitted = $submission ? $submission->is_submitted : false;

        // Get total score from the activity
        $total_score = $activity->points;

        // Get assigned score if submission exists, otherwise 0
        $assigned_score = $submission ? $submission->score : 0;

        // Determine submission status:
        if ($activity->is_submitted == true) {
            $status = 'Submitted'; // User has submitted
        } elseif ($activity->is_missing == true) {
            $status = 'Missing'; // Due date has passed, no submission
        } else {
            $status = 'Pending'; // Due date not yet passed, submission still possible
        }

        return response()->json([
            'submission_status' => $isSubmitted, // Returns true if submitted
            'assigned_score' => $assigned_score,
            'total_score' => $total_score,
            'status' => $status
        ]);
    }


    public function index()
    {
        return view('instructor.pages.class');
    }
    public function viewStudentsAndTeacher($classlist_id)
    {
        -
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
    $activity = Activity::where('id', $id)->with(['user'])->first();

    if (!$activity) {
        return abort(404, 'Activity not found.');
    }

    $classlist = Classlist::where('id', $activity->classlist_id)->with(['user'])->first();

    $students = JoinedClass::where('classlist_id', $activity->classlist_id)
        ->with('user')
        ->get();

    // Fetch student scores
    foreach ($students as $student) {
        $output = Output::where('user_id', $student->user->id)
            ->where('activity_id', $id)
            ->first();

        $student->score = $output ? $output->score : '--'; // Assign score or "--" if not found
    }

    return view('instructor.pages.activity', compact('activity', 'students'));
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
            ->whereNull('joined_classes.deleted_at') // Ensures only active students
            ->select('users.id', 'users.name', 'users.avatar', 'users.email')
            ->get();
        return view('instructor.pages.class', compact('activities', 'classlist', 'instructor', 'students'));
    }
    public function list($id)
    {
        $classlist = Classlist::where('is_archive', false)
            ->with(['section', 'user'])->find($id);
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
        // Validate request data
        $request->validate([
            'title' => 'required|string|max:255',
            'instruction' => 'nullable|string',
            'points' => 'required|integer|min:0',
            'due_date' => 'required|date',
            'due_time' => 'required',
            'user_id' => 'required|exists:users,id',
            'classlist_id' => 'required|exists:classlists,id',
            'section_id' => 'required|exists:sections,id',
            'selected_classes' => 'nullable|array', // Selected class IDs when sharing
            'selected_classes.*' => 'exists:classlists,id' // Ensure valid class IDs
        ]);

        // Create the primary activity
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

        // If "Share Activity" is checked, create copies for selected classes
        if ($request->has('share_activity') && $request->selected_classes) {
            foreach ($request->selected_classes as $classId) {
                Activity::create([
                    'user_id' => $request->user_id,
                    'section_id' => $request->section_id,
                    'classlist_id' => $classId, // Assign to the selected class
                    'title' => $request->title,
                    'instruction' => $request->instruction,
                    'points' => $request->points,
                    'due_date' => $request->due_date,
                    'due_time' => $request->due_time,
                    'accessible_date' => $request->accessible_date ?? null,
                    'accessible_time' => $request->accessible_time ?? null
                ]);
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
    public function removeStudent(Request $request){
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
}
