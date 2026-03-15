<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;

class CourseService
{
    /**
     * Get all courses.
     */
    public function getAllCourses()
    {
        return Course::all();
    }

    /**
     * Create a new course.
     */
    public function createCourse(array $data)
    {
        return Course::create($data);
    }

    /**
     * Get a course by ID.
     */
    public function getCourseById(string $id)
    {
        return Course::findOrFail($id);
    }

    /**
     * Update an existing course.
     */
    public function updateCourse(string $id, array $data)
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return $course;
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
