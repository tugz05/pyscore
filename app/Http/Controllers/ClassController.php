<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classlist;
use App\Models\Output;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function getSubmissionStatus($userId, $activityId)
    {
        // Check if the user has already submitted the activity
        $submission = Output::where('user_id', $userId)
                            ->where('activity_id', $activityId)
                            ->exists();
        $total_score = Activity::find($activityId)->points;
        $output = Output::where('user_id', $userId)
        ->where('activity_id', $activityId);
        $assigned_score = $output->exists() ? $output->first()->score : 0;
        return response()->json([
            'submission_status' => $submission,
            'assigned_score' => $assigned_score,
            'total_score' => $total_score,
            'status' => $submission ? 'Submitted' : 'Missing'
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
        $activity = Activity::where('id', $id)
            ->with(['user']) // Load relationships
            ->first();
            // dd($activity);
        return view('instructor.pages.activity', compact('activity'));
    }

    public function viewClass($id)
    {
        $activities = Activity::where('classlist_id', $id)
            ->with(['classlist', 'section', 'user']) // Load relationships
            ->orderBy('created_at', 'desc') // Sort by latest time
            ->get();

        // Fetch the classlist details separately
        $classlist = Classlist::with('section', 'user')->where('id', $id)->first();

        // Fetch the teacher assigned to the class (modify this as needed based on your DB structure)
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
        return view('instructor.pages.class', compact('activities', 'classlist','instructor', 'students'));
    }
    public function list($id)
    {
        $classlist = Classlist::with(['section', 'user'])->find($id);
        $activities = Activity::where('classlist_id', $id)
            ->with(['classlist', 'section','user']) // Load relationships
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
        $activity = Activity::create($request->all());
        // dd($activity);
        return response()->json(['message' => 'Activity added successfully!', 'activity' => $activity]);
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
}
