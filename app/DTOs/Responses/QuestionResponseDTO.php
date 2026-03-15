<?php

namespace App\DTOs\Responses;

use App\Models\Question;

class QuestionResponseDTO
{
    public int $id;
    public string $question;
    public array $options;

    public function __construct(Question $question)
    {
        $this->id = $question->id;
        $this->question = $question->question;
        $this->options = $question->options;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'options' => $this->options,
        ];
    }
}
