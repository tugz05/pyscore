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
        $users = User::where('account_type', 'student')->get();

        if (request()->ajax()) {
            return response()->json(['data' => $users]);
        }

        return view('admin.pages.student', compact('users'));
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
            'account_type' => 'required|in:student,instructor,admin',
        ]);

        $user = User::find($request->id);
        $user->account_type = $request->account_type;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found.'], 404);
    }

    $user->delete();

    return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }

}
