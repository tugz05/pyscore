<?php

namespace App\Http\Controllers;

use App\Models\Classlist;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClasslistController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        return view('instructor.index', compact('sections'));
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
                'room' => 'required|string|max:50',
            ]);
            $user = Auth::user()->id;
            $classlist = new Classlist();
            $classlist->user_id = $user;
            $classlist->name = $validated['name'];
            $classlist->section_id = $validated['section_id'];
            $classlist->academic_year = $validated['academic_year'];
            $classlist->room = $validated['room'];
            $classlist->save(); // Save manually

            return response()->json(['success' => 'Class added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'academic_year' => 'required|string',
            'room' => 'required|string|max:50',
        ]);

        $classlist = Classlist::findOrFail($id);
        $classlist->update($request->all());

        return response()->json(['success' => 'Class updated successfully!']);
    }

    public function destroy($id)
    {
        Classlist::findOrFail($id)->delete();
        return response()->json(['success' => 'Class deleted successfully!']);
    }
}
