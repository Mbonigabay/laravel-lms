<?php

namespace App\Services;

use App\Models\Course;
use App\DTOs\Responses\CourseReportResponseDTO;

class ReportService
{
    /**
     * Get courses with their student count.
     * @return array
     */
    public function getCoursesWithStudentCount(): array
    {
        $courses = Course::withCount('students')->get();
        return CourseReportResponseDTO::collection($courses);
    }
}
