<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\User;

class QuizService
{
    /**
     * Create a new quiz for a course.
     */
    public function createQuiz(string $courseId, array $data)
    {
        $course = Course::findOrFail($courseId);
        return $course->quizzes()->create($data);
    }

    /**
     * Add a question to a quiz.
     */
    public function addQuestion(string $quizId, array $data)
    {
        $quiz = Quiz::findOrFail($quizId);
        return $quiz->questions()->create($data);
    }

    /**
     * Submit a quiz and calculate the score.
     */
    public function submitQuiz(User $user, string $quizId, array $answers)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        
        $score = 0;
        $totalQuestions = $quiz->questions->count();
        
        foreach ($quiz->questions as $question) {
            $submittedAnswer = $answers[$question->id] ?? null;
            if ($submittedAnswer === $question->answer) {
                $score++;
            }
        }
        
        $submission = $quiz->submissions()->create([
            'user_id' => $user->id,
            'answers' => $answers,
            'score' => $score,
        ]);
        
        return [
            'score' => $score,
            'total' => $totalQuestions,
            'submission' => $submission
        ];
    }
}
