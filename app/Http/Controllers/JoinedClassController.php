<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classlist;
use App\Models\JoinedClass;
use App\Models\Output;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinedClassController extends Controller
{
    public function joinClass($classId)
    {
        $class = Classlist::find($classId);
        if (!$class) {
            return response()->json(['error' => 'Class not found!'], 404);
        }
        return view('user.pages.join_class', compact('class'), ['id' => $classId]);
    }
    public function list($id)
{
    $classlist = Classlist::with(['section', 'user'])->find($id);
    $activities = Activity::where('classlist_id', $id)
        ->with(['classlist', 'section', 'user']) // Load relationships
        ->orderBy('created_at', 'desc') // Sort by latest time
        ->get()
        ->map(function ($activity) {
            // Check if the logged-in user has an output for this activity
            $output = Output::where('user_id', auth()->id())
                ->where('activity_id', $activity->id)
                ->first();

            $activity->user_score = $output ? $output->score : null; // Store score or null
            return $activity;
        });

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
    $currentDate = Carbon::now()->toDateString();
    $currentTime = Carbon::now()->toTimeString();

    $activity = Activity::where('classlist_id', $id)
        ->where(function ($query) use ($currentDate, $currentTime) {
            $query->whereNull('accessible_date')
                ->orWhere(function ($subQuery) use ($currentDate, $currentTime) {
                    $subQuery->where('accessible_date', $currentDate)
                        ->where(function ($timeQuery) use ($currentTime) {
                            $timeQuery->whereNull('accessible_time')
                                ->orWhere('accessible_time', '<=', $currentTime);
                        });
                });
        })
        ->with(['classlist', 'section', 'user'])
        ->get();

    // Fetch class details
    $classlist = Classlist::with('section', 'user')->where('id', $id)->first();

    // Ensure $instructor is defined
    $instructor = $classlist->user ?? null;

    // Fetch students who joined the class
    $students = JoinedClass::where('classlist_id', $id)
        ->with('user')
        ->get()
        ->pluck('user');

    return view('user.pages.class', compact('activity', 'classlist', 'instructor', 'students'));
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
            $class = Classlist::find($validated['classlist_id']);
            if ($existingClass) {
                return response()->json(['error' => 'You have already joined this class!'], 400);
            } else if (!$class) {
                return response()->json(['error' => 'Class not found!'], 404);
            } else {
                $classlist = new JoinedClass();
                $classlist->user_id = $user;
                $classlist->classlist_id = $validated['classlist_id'];
                $classlist->date_joined = now();
                $classlist->save(); // Save manually

                return response()->json(['success' => 'Class joined successfully!']);
            }
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
