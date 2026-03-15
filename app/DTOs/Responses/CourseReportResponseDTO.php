<?php

namespace App\DTOs\Responses;

use App\Models\Course;

class CourseReportResponseDTO
{
    public int $id;

    public string $title;

    public int $students_count;

    public function __construct(Course $course)
    {
        $this->id = $course->id;
        $this->title = $course->title;
        // The students_count attribute must be present from withCount()
        $this->students_count = $course->students_count ?? 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'students_count' => $this->students_count,
        ];
    }

    public static function collection($courses): array
    {
        return $courses->map(function ($course) {
            return (new self($course))->toArray();
        })->toArray();
    }
}
