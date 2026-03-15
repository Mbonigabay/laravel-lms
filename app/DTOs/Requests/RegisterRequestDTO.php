<?php

namespace App\DTOs\Requests;

class RegisterRequestDTO
{
    public string $name;
    public string $email;
    public string $password;
    public ?string $role;

    public function __construct(string $name, string $email, string $password, ?string $role = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'] ?? null
        );
    }
}
