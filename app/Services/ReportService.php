<?php

namespace App\Services;

use App\DTOs\Responses\CourseReportResponseDTO;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    /**
     * Get courses with their student count.
     */
    public function getCoursesWithStudentCount(): array
    {
        return Cache::remember('reports:student_counts', 3600, function () {
            $courses = Course::withCount('students')->get();

            return CourseReportResponseDTO::collection($courses);
        });
    }
}
