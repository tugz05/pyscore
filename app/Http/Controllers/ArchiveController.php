<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Classlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    public function archiveData(Request $request)
    {
        try {
            $class = Classlist::findOrFail($request->id);
            $class->is_archive = 1; // Adjust this field based on your database structure
            $class->save();

            $archive = Archive::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'classlist_id' => $request->id
                ],
            );

            return response()->json(['success' => true, 'message' => 'Data archived successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to archive data']);
        }
    }
    public function getArchivelists()
    {

    }
    public function index()
    {
        $id = Auth::id();
        $archives = Archive::where('user_id', $id)->get();
        return view('instructor.pages.archive', compact('archives'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Archive $archive)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Archive $archive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Archive $archive)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Archive $archive)
    {
        //
    }
}
