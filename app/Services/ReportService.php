<?php

namespace App\Services;

use App\Models\Course;

class ReportService
{
    /**
     * Get courses with their student count.
     */
    public function getCoursesWithStudentCount()
    {
        return Course::withCount('students')->get();
    }
}
