<?php

namespace App\Http\Controllers;

use App\Models\Classlist;
use App\Models\Section;
use App\Models\Room;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClasslistController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $sections = Section::where('user_id', $userId)->get();
        $classlists = Classlist::where('user_id', $userId)->where('is_archive', 0)->get();
        $rooms = Room::all(); // Fetch all rooms
        $academic_year = AcademicYear::all(); // Fetch all academic years
        return view('instructor.index', compact('sections', 'classlists', 'rooms', 'academic_year'));
    }


    public function getClasslists()
    {
        // Assuming the class creator is stored as `user_id`
        $userId = auth()->id(); // Get the logged-in user's ID

        $classes = Classlist::with('section')
            ->where('user_id', $userId) // Filter classes by creator
            ->where('is_archive', false) // Filter classes by creator
            ->get();

        return response()->json(["data" => $classes]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'section_id' => 'required|exists:sections,id',
                'academic_year' => 'required|string',
                'room' => 'required|exists:rooms,room_number',
            ]);

            $userId = Auth::id();

            // Check if class with the same details already exists
            $existing = Classlist::where('name', $validated['name'])
                ->where('section_id', $validated['section_id'])
                ->where('academic_year', $validated['academic_year'])
                ->where('room', $validated['room'])
                ->where('user_id', $userId)
                ->where('is_archive', false)
                ->first();

            if ($existing) {
                return response()->json(['error' => 'This class already exists.'], 409);
            }

            // If no duplicate, create the class
            $classlist = new Classlist();
            $classlist->user_id = $userId;
            $classlist->name = $validated['name'];
            $classlist->section_id = $validated['section_id'];
            $classlist->academic_year = $validated['academic_year'];
            $classlist->room = $validated['room'];
            $classlist->course_image = $this->getUniqueRandomCourseImage();
            $classlist->save();

            return response()->json(['success' => 'Class added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Function to ensure different images are picked each time
    private function getUniqueRandomCourseImage()
    {
        $images = [
            'course_image1.png',
            'course_image2.png',
            'course_image3.png',
            'course_image4.png',
            'course_image5.png',
            'course_image6.png',
            'course_image7.png',
            'course_image8.png',
            'course_image9.png',
            'course_image10.png',
            'course_image11.png',
            'course_image12.png',
            'course_image13.png',
            'course_image14.png',
            'course_image15.png',
        ];

        // Shuffle the array to get a new random order
        shuffle($images);

        // Return a random image
        return $images[array_rand($images)];
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'academic_year' => 'required|string',
            'room' => 'required|string|max:50',
        ]);

        $userId = Auth::id();

        // Check for duplicate class excluding the current one
        $duplicate = Classlist::where('id', '!=', $id)
            ->where('name', $validated['name'])
            ->where('section_id', $validated['section_id'])
            ->where('academic_year', $validated['academic_year'])
            ->where('room', $validated['room'])
            ->where('user_id', $userId)
            ->where('is_archive', false)
            ->first();

        if ($duplicate) {
            return response()->json(['error' => 'A class with these details already exists.'], 409);
        }

        $classlist = Classlist::findOrFail($id);
        $classlist->update($validated);

        return response()->json(['success' => 'Class updated successfully!']);
    }

    public function destroy($id)
    {
        Classlist::findOrFail($id)->delete();
        return response()->json(['success' => 'Class deleted successfully!']);
    }



}
