<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;

class CourseController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/courses",
     *      operationId="getCoursesList",
     *      tags={"Courses"},
     *      summary="Get list of all courses",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return response()->json(Course::all());
    }

    /**
     * @OA\Post(
     *      path="/api/courses",
     *      operationId="storeCourse",
     *      tags={"Courses"},
     *      summary="Create a new course (Admin only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title"},
     *              @OA\Property(property="title", type="string", example="Advanced Laravel"),
     *              @OA\Property(property="description", type="string", example="Deep dive into internals.")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $course = Course::create($validated);
        return response()->json($course, 201);
    }

    /**
     * @OA\Get(
     *      path="/api/courses/{id}",
     *      operationId="getCourseById",
     *      tags={"Courses"},
     *      summary="Get course details",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Course ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course);
    }

    /**
     * @OA\Put(
     *      path="/api/courses/{id}",
     *      operationId="updateCourse",
     *      tags={"Courses"},
     *      summary="Update existing course (Admin only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Course ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string", example="Updated Title"),
     *              @OA\Property(property="description", type="string", example="Updated Description")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *      path="/api/courses/{id}",
     *      operationId="deleteCourse",
     *      tags={"Courses"},
     *      summary="Delete a course (Admin only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Course ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=204, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *      path="/api/courses/{id}/enroll",
     *      operationId="enrollInCourse",
     *      tags={"Courses"},
     *      summary="Enroll in a course (Student only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Course ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successfully enrolled"),
     *      @OA\Response(response=400, description="Already enrolled"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/api/courses/enrolled",
     *      operationId="getEnrolledCourses",
     *      tags={"Courses"},
     *      summary="Get list of enrolled courses for the logged-in student",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function enrolledCourses(Request $request)
    {
        return response()->json($request->user()->enrolledCourses);
    }

    /**
     * @OA\Get(
     *      path="/api/courses/{id}/students",
     *      operationId="getCourseStudents",
     *      tags={"Courses"},
     *      summary="Get list of students enrolled in the course (Teacher only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Course ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function students(string $id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course->students);
    }
}
