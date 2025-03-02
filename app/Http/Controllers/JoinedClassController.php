<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classlist;
use App\Models\JoinedClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinedClassController extends Controller
{
    public function joinClass($classId){
        $class = Classlist::find($classId);
        if(!$class){
            return response()->json(['error' => 'Class not found!'], 404);
        }
        return view('user.pages.join_class',compact('class'), ['id' => $classId]);
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
    public function getClasslists(Request $request)
    {
        // Get the classlist IDs that the authenticated user has joined
        $joined_class_id = JoinedClass::where('user_id', auth()->user()->id)->pluck('classlist_id');

        // Fetch only the classlists the user has joined
        $classlists = Classlist::whereIn('id', $joined_class_id)
            ->with(['section', 'instructor']) // ✅ Load related section & instructor
            ->get();

        return response()->json(["data" => $classlists]);
    }

    public function viewActivity($id)
    {
        $activity = Activity::where('id', $id)
            ->with(['user']) // Load relationships
            ->first();
            // dd($activity);
        return view('user.pages.activity', compact('activity'));
    }
    public function viewClass($id)
    {
        $activity = Activity::where('classlist_id', $id)
            ->with(['classlist', 'section', 'user']) // Load relationships
            ->get();

        // Fetch the classlist details separately
        $classlist = Classlist::with('section', 'user')->where('id', $id)->first();
        // dd($classlist);
        return view('user.pages.class', compact('activity', 'classlist'));
    }
    public function index()
    {
        $user = auth()->user();
        return view('user.home', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'classlist_id' => 'required|string|max:255',
        ]);

        $user = Auth::user()->id;

        // Check if the user has already joined the class
        $existingClass = JoinedClass::where('user_id', $user)
            ->where('classlist_id', $validated['classlist_id'])
            ->exists();

        if ($existingClass) {
            return response()->json(['error' => 'You have already joined this class!'], 400);
        }

        $classlist = new JoinedClass();
        $classlist->user_id = $user;
        $classlist->classlist_id = $validated['classlist_id'];
        $classlist->date_joined = now();
        $classlist->save(); // Save manually

        return response()->json(['success' => 'Class joined successfully!']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(JoinedClass $joinedClass)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JoinedClass $joinedClass)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JoinedClass $joinedClass)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JoinedClass $joinedClass)
    {
        //
    }
}
