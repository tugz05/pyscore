<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function getSections()
    {
        $id = Auth::id();
        $sections = Section::where('user_id', $id)->get();
        return response()->json(["data" => $sections]);
    }
    public function index()
    {
        $id = Auth::id();
        $sections = Section::where('user_id', $id)->get();
        return view('instructor.pages.sections', compact('sections'));
    }

    public function create()
    {
        return view('sections.create');
    }

    public function store(Request $request)
    {
        $id = Auth::id();
        $request->merge(['user_id' => $id]);
        $request->validate([
            'name' => 'required|string|max:255',
            'schedule_from' => 'required',
            'schedule_to' => 'required|after:schedule_from',
            'day' => 'required|string',
        ]);

        Section::create($request->all());

        return redirect()->route('sections.index')->with('success', 'Section added successfully!');
    }

    public function show(Section $section)
    {
        return view('sections.show', compact('section'));
    }

    public function edit(Section $section)
    {
        return view('sections.edit', compact('section'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'schedule_from' => 'required',
            'schedule_to' => 'required|after:schedule_from',
            'day' => 'required|string',
        ]);

        $section = Section::findOrFail($id);
        $section->update($request->all());

        return response()->json(['success' => 'Section updated successfully!']);
    }

    public function destroy($id)
    {
        Section::findOrFail($id)->delete();
        return response()->json(['success' => 'Section deleted successfully!']);
    }
}
