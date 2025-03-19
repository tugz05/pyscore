<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classlist;
use App\Models\JoinedClass;
use App\Models\Output;
use App\Models\AcademicYear;

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
            ->with(['classlist', 'section', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($activity) {
                $output = Output::where('user_id', auth()->id())
                    ->where('activity_id', $activity->id)
                    ->first();

                $activity->user_score = $output ? $output->score : null;
                return $activity;
            });

        return response()->json([
            'data' => $activities,
            'classlist' => $classlist,

        ]);
    }


    public function getArchives(Request $request)
    {
        // Get the classlist IDs that the authenticated user has joined
        $joined_class_id = JoinedClass::where('is_remove', false)
            ->where('user_id', auth()->user()->id)->pluck('classlist_id');

        // Fetch only the classlists the user has joined
        $classlists = Classlist::whereIn('id', $joined_class_id)
            ->where('is_archive', true)
            ->with(['section', 'instructor']) // ✅ Load related section & instructor
            ->get();

        return response()->json(["data" => $classlists]);
    }

    public function getClasslists(Request $request)
    {
        // Get the classlist IDs that the authenticated user has joined
        $joined_class_id = JoinedClass::where('is_remove', false)
            ->where('user_id', auth()->user()->id)->pluck('classlist_id');

        // Fetch only the classlists the user has joined
        $classlists = Classlist::whereIn('id', $joined_class_id)
            ->where('is_archive', false)
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
        $academic_year = AcademicYear::all(); // Fetch all academic years

        return view('user.home', compact('user', 'academic_year'));
    }
    public function archive()
    {
        $user = auth()->user();
        $academic_year = AcademicYear::all(); // Fetch all academic years

        return view('user.pages.archive', compact('user', 'academic_year'));
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
        // Validate the input
        $validated = $request->validate([
            'classlist_id' => 'required|string|max:255',
        ]);

        $userId = Auth::id();

        // Check if the class exists
        $class = Classlist::find($validated['classlist_id']);
        if (!$class) {
            return response()->json(['error' => 'Class not found!'], 404);
        }

        // Check if the class is archived
        if ($class->is_archive) {
            return response()->json(['error' => 'Class has been archived!'], 400);
        }

        // Check if the user has already joined the class and is active
        $existingClass = JoinedClass::where('user_id', $userId)
            ->where('classlist_id', $validated['classlist_id'])
            ->where('is_remove', false)
            ->exists();

        if ($existingClass) {
            return response()->json(['error' => 'You have already joined this class!'], 400);
        }

        // Check if the user has previously unenrolled and re-enroll them
        $unenrolledClass = JoinedClass::where('user_id', $userId)
            ->where('classlist_id', $validated['classlist_id'])
            ->where('is_remove', true)
            ->first();

        if ($unenrolledClass) {
            $unenrolledClass->is_remove = false;
            $unenrolledClass->date_joined = now(); // Update join date
            $unenrolledClass->save();

            return response()->json(['success' => 'Class rejoined successfully!']);
        }

        // If it's a new join, create a new record
        JoinedClass::create([
            'user_id' => $userId,
            'classlist_id' => $validated['classlist_id'],
            'date_joined' => now(),
            'is_remove' => false,
        ]);

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
    public function destroy(Request $request)
    {
        try {
            // Find the class based on id and user_id
            $class = JoinedClass::where('classlist_id', $request->id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Update the is_remove field
            $class->is_remove = 1;
            $class->save();

            return response()->json(['success' => true, 'message' => 'Class unenrolled successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unenroll class',
                'error' => $e->getMessage() // Optional: for debugging purposes
            ]);
        }
    }
}
