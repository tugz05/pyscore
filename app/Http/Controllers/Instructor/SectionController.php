<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function getSections()
    {
        return response()->json(["data" => Section::all()]);
    }
    public function index()
    {
        $sections = Section::all();
        return view('instructor.pages.sections', compact('sections'));
    }

    public function create()
    {
        return view('sections.create');
    }

    public function store(Request $request)
    {
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
