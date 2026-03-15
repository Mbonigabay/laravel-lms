<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_trace_id_in_response_headers_and_body(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/courses');

        $response->assertStatus(200);
        $response->assertHeader('X-Trace-Id');
        $response->assertJsonStructure([
            'meta' => ['trace_id'],
        ]);

        $this->assertEquals(
            $response->headers->get('X-Trace-Id'),
            $response->json('meta.trace_id')
        );
    }

    public function test_admin_can_create_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->postJson('/api/courses', [
                'title' => 'New Course',
                'description' => 'Course Description',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('courses', ['title' => 'New Course']);
    }

    public function test_student_cannot_create_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)
            ->postJson('/api/courses', [
                'title' => 'Unauthorized Course',
                'description' => 'Should fail',
            ]);

        $response->assertStatus(403);
    }

    public function test_student_can_enroll_in_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create();

        $response = $this->actingAs($student)
            ->postJson("/api/courses/{$course->id}/enroll");

        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }
}
