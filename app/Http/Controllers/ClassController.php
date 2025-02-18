<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index() {
        return view('instructor.pages.class');
    }
}
