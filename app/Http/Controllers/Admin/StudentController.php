<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $requestedUpgrades = User::where('isRequested', true)
        ->where('account_type', 'student') // Ensure only students who requested an upgrade are shown
        ->get();

    if (request()->ajax()) {
        return response()->json(['data' => $requestedUpgrades]); // Ensure 'data' key exists
    }

    return view('admin.pages.student', compact('requestedUpgrades'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'account_type' => 'required|in:student,instructor',
        ]);

        $user = User::find($request->id);
        $user->account_type = $request->account_type;
        $user->isRequested = null; // Reset the request flag
        $user->request_status = null; // Reset request_status so they can request again
        $user->save();

        return response()->json(['success' => true]);
    }


    public function denyUpgradeRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->id);
        $user->isRequested = null; // Reset the request
        $user->request_status = 'denied'; // Mark as denied
        $user->save();

        return response()->json(['success' => true]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
