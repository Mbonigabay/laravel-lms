<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;

class ReportController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/reports/courses",
     *      operationId="getCoursesReport",
     *      tags={"Reports"},
     *      summary="Get list of courses with student counts (Admin only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function coursesReport()
    {
        $courses = Course::withCount('students')->get();
        return response()->json($courses);
    }
}
