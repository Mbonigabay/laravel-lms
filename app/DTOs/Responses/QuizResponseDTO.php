<?php

namespace App\DTOs\Responses;

use App\Models\Quiz;

class QuizResponseDTO
{
    public int $id;
    public string $title;

    public function __construct(Quiz $quiz)
    {
        $this->id = $quiz->id;
        $this->title = $quiz->title;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
        ];
    }
}
