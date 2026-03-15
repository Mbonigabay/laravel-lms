<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;

class QuizController extends Controller
{
    public function storeQuiz(Request $request, string $courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $quiz = $course->quizzes()->create($validated);
        
        return response()->json($quiz, 201);
    }

    public function storeQuestion(Request $request, string $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        
        $validated = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'answer' => 'required|string',
        ]);
        
        if (!in_array($validated['answer'], $validated['options'])) {
            return response()->json(['message' => 'Answer must be one of the options.'], 422);
        }
        
        $question = $quiz->questions()->create($validated);
        
        return response()->json($question, 201);
    }

    public function submitQuiz(Request $request, string $quizId)
    {
        $quiz = Quiz::with('questions')->findOrFail($quizId);
        $user = $request->user();
        
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);
        
        $score = 0;
        $totalQuestions = $quiz->questions->count();
        
        foreach ($quiz->questions as $question) {
            $submittedAnswer = $validated['answers'][$question->id] ?? null;
            if ($submittedAnswer === $question->answer) {
                $score++;
            }
        }
        
        $submission = $quiz->submissions()->create([
            'user_id' => $user->id,
            'answers' => $validated['answers'],
            'score' => $score,
        ]);
        
        return response()->json([
            'message' => 'Quiz submitted successfully',
            'score' => $score,
            'total' => $totalQuestions,
            'submission' => $submission
        ], 201);
    }
}
