<?php

namespace App\DTOs\Requests;

class CreateQuizRequestDTO
{
    public string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title']
        );
    }
}
