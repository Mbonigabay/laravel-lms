<?php

namespace App\DTOs\Requests;

class CreateCourseRequestDTO
{
    public string $title;
    public ?string $description;

    public function __construct(string $title, ?string $description = null)
    {
        $this->title = $title;
        $this->description = $description;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'] ?? null
        );
    }
}
