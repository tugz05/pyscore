<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classlist;
use App\Models\Output;
use Illuminate\Http\Request;

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
        $classlist = Classlist::with('section', 'user')
        ->where('id', $id)->first();
        return view('instructor.pages.class', compact('activities', 'classlist'));
    }
    public function list($id)
    {
        $classlist = Classlist::where('is_archive', false)
        ->with(['section', 'user'])->find($id);
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
