<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Traits\ApiResponse;

class ReportController extends Controller
{
    use ApiResponse;

    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @OA\Get(
     *      path="/api/reports/courses",
     *      operationId="getCoursesReport",
     *      tags={"Reports"},
     *      summary="Get list of courses with student counts (Admin only)",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function coursesReport()
    {
        return $this->successResponse($this->reportService->getCoursesWithStudentCount(), 'Course report retrieved successfully');
    }
}
