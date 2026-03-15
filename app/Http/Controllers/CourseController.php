<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        return response()->json(Course::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $course = Course::create($validated);
        return response()->json($course, 201);
    }

    public function show(string $id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course);
    }

    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $course->update($validated);
        return response()->json($course);
    }

    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(null, 204);
    }

    public function enroll(Request $request, string $id)
    {
        $course = Course::findOrFail($id);
        $user = $request->user();

        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return response()->json(['message' => 'Already enrolled'], 400);
        }

        $user->enrollments()->create(['course_id' => $course->id]);
        return response()->json(['message' => 'Successfully enrolled in course.'], 200);
    }

    public function enrolledCourses(Request $request)
    {
        return response()->json($request->user()->enrolledCourses);
    }

    public function students(string $id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course->students);
    }
}
