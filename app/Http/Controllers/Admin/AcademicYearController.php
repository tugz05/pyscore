<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Validator;


class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.academic_year');
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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'semester' => 'required|string',
            'start_year' => 'required|integer|min:2024|max:2099',
            'end_year' => 'required|integer|min:2024|max:2099|gt:start_year',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Store academic year in DB
        $academic_year = AcademicYear::create([
            'semester' => $request->semester,
            'start_year' => $request->start_year,
            'end_year' => $request->end_year,
        ]);

        return response()->json(['message' => 'Academic Year added successfully!', 'data' => $academic_year], 200);
    }
    public function list()
    {
        $academic_year = AcademicYear::all();
        return response()->json(['data' => $academic_year]);
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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'semester' => 'required|string',
            'start_year' => 'required|integer|min:2024|max:2099',
            'end_year' => 'required|integer|min:2024|max:2099|gt:start_year',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update academic year in DB
        $academic_year = AcademicYear::find($id);
        $academic_year->update([
            'semester' => $request->semester,
            'start_year' => $request->start_year,
            'end_year' => $request->end_year,
        ]);

        return response()->json(['message' => 'Academic Year updated successfully!', 'data' => $academic_year], 200);
    }
}
