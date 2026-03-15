<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\DTOs\Requests\CreateCourseRequestDTO;
use App\DTOs\Requests\UpdateCourseRequestDTO;
use App\DTOs\Responses\CourseResponseDTO;

class CourseService
{
    /**
     * Get all courses.
     * @return array
     */
    public function getAllCourses(): array
    {
        $courses = Course::all();
        return CourseResponseDTO::collection($courses);
    }

    /**
     * Create a new course.
     * @param CreateCourseRequestDTO $dto
     * @return CourseResponseDTO
     */
    public function createCourse(CreateCourseRequestDTO $dto): CourseResponseDTO
    {
        $course = Course::create([
            'title' => $dto->title,
            'description' => $dto->description,
        ]);
        return new CourseResponseDTO($course);
    }

    /**
     * Get a course by ID.
     * @param string $id
     * @return CourseResponseDTO
     */
    public function getCourseById(string $id): CourseResponseDTO
    {
        $course = Course::findOrFail($id);
        return new CourseResponseDTO($course);
    }

    /**
     * Update an existing course.
     * @param string $id
     * @param UpdateCourseRequestDTO $dto
     * @return CourseResponseDTO
     */
    public function updateCourse(string $id, UpdateCourseRequestDTO $dto): CourseResponseDTO
    {
        $course = Course::findOrFail($id);
        
        $updateData = $dto->getUpdateData();
        if (!empty($updateData)) {
            $course->update($updateData);
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
        return true;
    }

    /**
     * Enroll a user in a course.
     */
    public function enrollUser(User $user, string $courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return false;
        }

        $user->enrollments()->create(['course_id' => $course->id]);
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
}
