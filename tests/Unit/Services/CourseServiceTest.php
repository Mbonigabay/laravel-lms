<?php

namespace Tests\Unit\Services;

use App\DTOs\Requests\CreateCourseRequestDTO;
use App\Exceptions\ApiException;
use App\Models\Course;
use App\Models\User;
use App\Services\CourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    private CourseService $courseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courseService = new CourseService;
    }

    public function test_it_can_create_a_course_and_clears_cache(): void
    {
        Cache::shouldReceive('forget')->atLeast()->times(1);

        $dto = new CreateCourseRequestDTO(
            'Test Course',
            'Test Description'
        );

        $response = $this->courseService->createCourse($dto);

        $this->assertDatabaseHas('courses', ['title' => 'Test Course']);
        $this->assertEquals('Test Course', $response->title);
    }

    public function test_it_can_enroll_a_user(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $result = $this->courseService->enrollUser($user, $course->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_it_prevents_duplicate_enrollment(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->courseService->enrollUser($user, $course->id);

        $this->expectException(ApiException::class);
        $this->courseService->enrollUser($user, $course->id);
    }
}
