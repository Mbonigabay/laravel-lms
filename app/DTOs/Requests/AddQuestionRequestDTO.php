<?php

namespace App\DTOs\Requests;

class AddQuestionRequestDTO
{
    public string $question;
    public array $options;
    public string $answer;

    public function __construct(string $question, array $options, string $answer)
    {
        $this->question = $question;
        $this->options = $options;
        $this->answer = $answer;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['question'],
            $data['options'],
            $data['answer']
        );
    }
}
