<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classlist;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        return view('instructor.pages.class');
    }
    public function viewActivity($id)
    {
        $activity = Activity::findOrFail($id);
        return response()->json($activity);

    }

    public function viewClass($id)
    {
        $activities = Activity::where('classlist_id', $id)
            ->with(['classlist', 'section']) // Load relationships
            ->get();

        // Fetch the classlist details separately
        $classlist = Classlist::with('section')->where('id', $id)->first();
        return view('instructor.pages.class', compact('activities', 'classlist'));
    }
    public function list($id)
    {
        $activities = Activity::where('classlist_id', $id)
            ->with(['classlist', 'section']) // Load relationships
            ->get();
        return response()->json(["data" => $activities]);
    }
    public function store(Request $request)
    {
        $activity = Activity::create($request->all());
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
