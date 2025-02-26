<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('user.pages.settings');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function requestUpgrade()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    $user->isRequested = true;
    $user->request_status = 'pending'; // Mark request as pending
    $user->save();

    return response()->json(['success' => true]);
}

}
