<?php

namespace App\DTOs\Responses;

use App\Models\Course;

class CourseResponseDTO
{
    public int $id;

    public string $title;

    public ?string $description;

    public ?int $students_count;

    public function __construct(Course $course)
    {
        $this->id = $course->id;
        $this->title = $course->title;
        $this->description = $course->description;
        // Check if students_count attribute was added via withCount
        $this->students_count = $course->students_count ?? null;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
        ];

        if ($this->students_count !== null) {
            $data['students_count'] = $this->students_count;
        }

        return $data;
    }

    /**
     * Map a collection of courses to an array of DTO arrays.
     */
    public static function collection($courses): array
    {
        return $courses->map(function ($course) {
            return (new self($course))->toArray();
        })->toArray();
    }
}
