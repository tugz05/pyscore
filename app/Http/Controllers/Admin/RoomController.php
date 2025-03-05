<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;


class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.room');
    }
    public function list()
    {
        $room = Room::all();
        return response()->json(['data' => $room]);
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
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number|max:255',
        ]);

        $room = Room::create([
            'room_number' => $request->room_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room added successfully!',
            'data' => $room
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}


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
}
