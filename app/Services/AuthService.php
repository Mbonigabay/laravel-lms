<?php

namespace App\Services;

use App\DTOs\Requests\LoginRequestDTO;
use App\DTOs\Requests\RegisterRequestDTO;
use App\DTOs\Responses\AuthResponseDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user and return user instance.
     */
    public function register(RegisterRequestDTO $dto): User
    {
        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => $dto->role ?? 'student',
        ]);
    }

    /**
     * Authenticate a user and create token.
     *
     * @throws ValidationException
     */
    public function login(LoginRequestDTO $dto): AuthResponseDTO
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return new AuthResponseDTO($user, $token);
    }
}
