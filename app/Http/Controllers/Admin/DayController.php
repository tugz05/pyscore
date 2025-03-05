<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Day;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DayController extends Controller
{
    /**
     * Display the days page.
     */
    public function index()
    {
        return view('admin.pages.day');
    }

    /**
     * Get list of days.
     */
    public function list()
    {
        $days = Day::all();
        return response()->json(['data' => $days]);
    }

    /**
     * Store a newly created day.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:days,name|max:255',
        ]);

        $day = Day::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Day added successfully!',
            'data' => $day
        ]);
    }

    /**
     * Update the specified day.
     */
    public function update(Request $request, string $id)
    {
        $day = Day::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:days,name,' . $id . '|max:255',
        ]);

        $day->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Day updated successfully!',
            'data' => $day
        ]);
    }

    /**
     * Remove the specified day from storage.
     */
    public function destroy(string $id)
    {
        $day = Day::findOrFail($id);
        $day->delete();

        return response()->json([
            'success' => true,
            'message' => 'Day deleted successfully!'
        ]);
    }
}
