<?php

namespace Tests\Unit\Services;

use App\DTOs\Requests\LoginRequestDTO;
use App\DTOs\Requests\RegisterRequestDTO;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService;
    }

    public function test_it_can_register_a_user(): void
    {
        $dto = new RegisterRequestDTO(
            'John Doe',
            'john@example.com',
            'password123',
            'student'
        );

        $user = $this->authService->register($dto);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $this->assertEquals('John Doe', $user->name);
    }

    public function test_it_can_login_a_user(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
        ]);

        $dto = new LoginRequestDTO(
            'jane@example.com',
            'password123'
        );

        $response = $this->authService->login($dto);

        $this->assertEquals('jane@example.com', $response->user['email']);
        $this->assertNotEmpty($response->token);
    }

    public function test_it_throws_exception_on_invalid_login(): void
    {
        User::factory()->create([
            'email' => 'fail@example.com',
            'password' => Hash::make('password123'),
        ]);

        $dto = new LoginRequestDTO(
            'fail@example.com',
            'wrongpassword'
        );

        $this->expectException(ValidationException::class);
        $this->authService->login($dto);
    }
}
