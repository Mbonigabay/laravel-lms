<?php

namespace App\Services;

use App\Models\Course;
use App\DTOs\Responses\CourseReportResponseDTO;
use Illuminate\Support\Facades\Cache;

class ReportService
{
    /**
     * Get courses with their student count.
     * @return array
     */
    public function getCoursesWithStudentCount(): array
    {
        return Cache::remember('reports:student_counts', 3600, function () {
            $courses = Course::withCount('students')->get();
            return CourseReportResponseDTO::collection($courses);
        });
    }
}
