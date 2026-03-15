<?php

namespace App\DTOs\Responses;

use App\Models\User;

class AuthResponseDTO
{
    public array $user;

    public string $token;

    public function __construct(User $user, string $token)
    {
        $this->user = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];
        $this->token = $token;
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'token' => $this->token,
        ];
    }
}
