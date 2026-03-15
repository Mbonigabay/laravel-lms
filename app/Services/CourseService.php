<?php

namespace App\Services;

use App\DTOs\Requests\CreateCourseRequestDTO;
use App\DTOs\Requests\UpdateCourseRequestDTO;
use App\DTOs\Responses\CourseResponseDTO;
use App\Exceptions\ApiException;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CourseService
{
    private const CACHE_KEY_ALL = 'courses:all';

    private const CACHE_KEY_PREFIX = 'course:';

    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get all courses.
     */
    public function getAllCourses(): array
    {
        return Cache::remember(self::CACHE_KEY_ALL, self::CACHE_TTL, function () {
            $courses = Course::all();

            return CourseResponseDTO::collection($courses);
        });
    }

    /**
     * Create a new course.
     */
    public function createCourse(CreateCourseRequestDTO $dto): CourseResponseDTO
    {
        $course = Course::create([
            'title' => $dto->title,
            'description' => $dto->description,
        ]);

        $this->clearCourseCache();

        return new CourseResponseDTO($course);
    }

    /**
     * Get a course by ID.
     */
    public function getCourseById(string $id): CourseResponseDTO
    {
        return Cache::remember(self::CACHE_KEY_PREFIX.$id, self::CACHE_TTL, function () use ($id) {
            $course = Course::findOrFail($id);

            return new CourseResponseDTO($course);
        });
    }

    /**
     * Update an existing course.
     */
    public function updateCourse(string $id, UpdateCourseRequestDTO $dto): CourseResponseDTO
    {
        $course = Course::findOrFail($id);

        $updateData = $dto->getUpdateData();
        if (! empty($updateData)) {
            $course->update($updateData);
            $this->clearCourseCache($id);
        }

        return new CourseResponseDTO($course);
    }

    /**
     * Delete a course.
     */
    public function deleteCourse(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        $this->clearCourseCache($id);
        Cache::forget('reports:student_counts');

        return true;
    }

    /**
     * Enroll a user in a course.
     */
    public function enrollUser(User $user, string $courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            throw new ApiException('You are already enrolled in this course.', 400);
        }

        $user->enrollments()->create(['course_id' => $course->id]);

        // Invalidate report cache as student count changed
        Cache::forget('reports:student_counts');

        return true;
    }

    /**
     * Get list of enrolled courses for a user.
     */
    public function getEnrolledCourses(User $user)
    {
        return $user->enrolledCourses;
    }

    /**
     * Get list of students enrolled in a course.
     */
    public function getCourseStudents(string $courseId)
    {
        $course = Course::findOrFail($courseId);

        return $course->students;
    }

    /**
     * Clear relevant course caches.
     */
    private function clearCourseCache(?string $id = null): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
        if ($id) {
            Cache::forget(self::CACHE_KEY_PREFIX.$id);
        }
        // Reports depend on course data/counts
        Cache::forget('reports:student_counts');
    }
}
