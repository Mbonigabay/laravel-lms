<?php

namespace App\DTOs\Requests;

class UpdateCourseRequestDTO
{
    public ?string $title;

    public ?string $description;

    public function __construct(?string $title = null, ?string $description = null)
    {
        $this->title = $title;
        $this->description = $description;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? null,
            $data['description'] ?? null
        );
    }

    /**
     * Get only the provided fields to update.
     */
    public function getUpdateData(): array
    {
        $data = [];
        if ($this->title !== null) {
            $data['title'] = $this->title;
        }
        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        return $data;
    }
}
