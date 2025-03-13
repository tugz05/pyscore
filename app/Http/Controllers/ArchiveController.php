<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Archive;
use App\Models\Classlist;
use App\Models\JoinedClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    public function index()
    {
        $id = Auth::id();
        $archives = Archive::where('user_id', $id)->get();
        return view('instructor.pages.archive', compact('archives'));
    }

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
        $userId = auth()->id();

        // Get archived classes from the Archive table
        $archivedClasses = Archive::with('classlist.section')
            ->where('user_id', $userId)
            ->get();

        return response()->json(["data" => $archivedClasses]);
    }

    public function restoreClass(Request $request)
    {
        try {
            $archive = Archive::where('classlist_id', $request->id)->where('user_id', Auth::id())->first();

            if ($archive) {
                $archive->delete(); // Remove from archive
                Classlist::where('id', $request->id)->update(['is_archive' => false]); // Mark as active
                return response()->json(['success' => true, 'message' => 'Class restored successfully']);
            }

            return response()->json(['success' => false, 'message' => 'Class not found in archive']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to restore class']);
        }
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
    public function destroy($id)
    {
        try {
            // Find the archived record
            $archive = Archive::where('classlist_id', $id)->where('user_id', Auth::id())->first();

            if ($archive) {
                $archive->delete(); // Remove from archives
                Classlist::where('id', $id)->delete(); // Permanently delete classlist
                JoinedClass::where('classlist_id', $id)->delete();
                Activity::where('classlist_id', $id)->delete();

                return response()->json(['success' => true, 'message' => 'Class deleted successfully']);
            }

            return response()->json(['success' => false, 'message' => 'Class not found in archive']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete class']);
        }
    }
}
