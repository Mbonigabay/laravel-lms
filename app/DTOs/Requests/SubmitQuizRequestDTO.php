<?php

namespace App\DTOs\Requests;

class SubmitQuizRequestDTO
{
    public array $answers;

    public function __construct(array $answers)
    {
        $this->answers = $answers;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['answers']
        );
    }
}
