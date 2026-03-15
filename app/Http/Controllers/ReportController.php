<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;

class ReportController extends Controller
{
    public function coursesReport()
    {
        $courses = Course::withCount('students')->get();
        return response()->json($courses);
    }
}
