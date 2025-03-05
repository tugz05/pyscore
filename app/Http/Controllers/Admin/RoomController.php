<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display the rooms page.
     */
    public function index()
    {
        return view('admin.pages.room');
    }

    /**
     * Get list of rooms.
     */
    public function list()
    {
        $rooms = Room::all();
        return response()->json(['data' => $rooms]);
    }

    /**
     * Store a newly created room.
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
     * Update the specified room.
     */
    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $id . '|max:255',
        ]);

        $room->update([
            'room_number' => $request->room_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully!',
            'data' => $room
        ]);
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully!'
        ]);
    }
}
