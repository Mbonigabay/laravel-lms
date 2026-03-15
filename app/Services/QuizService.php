<?php

namespace App\Services;

use App\DTOs\Requests\AddQuestionRequestDTO;
use App\DTOs\Requests\CreateQuizRequestDTO;
use App\DTOs\Requests\SubmitQuizRequestDTO;
use App\DTOs\Responses\QuestionResponseDTO;
use App\DTOs\Responses\QuizResponseDTO;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;

class QuizService
{
    /**
     * Create a new quiz for a course.
     */
    public function createQuiz(string $courseId, CreateQuizRequestDTO $dto): QuizResponseDTO
    {
        $course = Course::findOrFail($courseId);
        /** @var Quiz $quiz */
        $quiz = $course->quizzes()->create([
            'title' => $dto->title,
        ]);

        return new QuizResponseDTO($quiz);
    }

    /**
     * Add a question to a quiz.
     */
    public function addQuestion(string $quizId, AddQuestionRequestDTO $dto): QuestionResponseDTO
    {
        $quiz = Quiz::findOrFail($quizId);
        /** @var Question $question */
        $question = $quiz->questions()->create([
            'question' => $dto->question,
            'options' => $dto->options,
            'answer' => $dto->answer,
        ]);

        return new QuestionResponseDTO($question);
    }

    /**
     * Submit a quiz and calculate the score.
     */
    public function submitQuiz(User $user, string $quizId, SubmitQuizRequestDTO $dto)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);

        $score = 0;
        $totalQuestions = $quiz->questions->count();

        foreach ($quiz->questions as $question) {
            /** @var Question $question */
            $submittedAnswer = $dto->answers[$question->id] ?? null;
            if ($submittedAnswer === $question->answer) {
                $score++;
            }
        }

        $submission = $quiz->submissions()->create([
            'user_id' => $user->id,
            'answers' => $dto->answers,
            'score' => $score,
        ]);

        return [
            'score' => $score,
            'total' => $totalQuestions,
            'submission' => $submission,
        ];
    }
}
