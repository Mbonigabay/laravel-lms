<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\QuizService;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * @OA\Post(
     *      path="/api/courses/{id}/quizzes",
     *      operationId="storeQuiz",
     *      tags={"Quizzes"},
     *      summary="Create a new quiz in a course (Teacher only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Course ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title"},
     *              @OA\Property(property="title", type="string", example="PHP Basics Quiz")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function storeQuiz(Request $request, string $courseId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $quiz = $this->quizService->createQuiz($courseId, $validated);
        
        return response()->json($quiz, 201);
    }

    /**
     * @OA\Post(
     *      path="/api/quizzes/{id}/questions",
     *      operationId="storeQuestion",
     *      tags={"Quizzes"},
     *      summary="Add a question to a quiz (Teacher only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Quiz ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"question","options","answer"},
     *              @OA\Property(property="question", type="string", example="What does PHP stand for?"),
     *              @OA\Property(property="options", type="array", @OA\Items(type="string"), example={"Python rules", "PHP: Hypertext Preprocessor"}),
     *              @OA\Property(property="answer", type="string", example="PHP: Hypertext Preprocessor")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=422, description="Validation error"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function storeQuestion(Request $request, string $quizId)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'answer' => 'required|string',
        ]);
        
        if (!in_array($validated['answer'], $validated['options'])) {
            return response()->json(['message' => 'Answer must be one of the options.'], 422);
        }
        
        $question = $this->quizService->addQuestion($quizId, $validated);
        
        return response()->json($question, 201);
    }

    /**
     * @OA\Post(
     *      path="/api/quizzes/{id}/submit",
     *      operationId="submitQuiz",
     *      tags={"Quizzes"},
     *      summary="Submit quiz answers (Student only)",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(name="id", description="Quiz ID", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"answers"},
     *              @OA\Property(property="answers", type="object", example={"1": "PHP: Hypertext Preprocessor"})
     *          )
     *      ),
     *      @OA\Response(response=201, description="Successfully submitted"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function submitQuiz(Request $request, string $quizId)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
        ]);
        
        $result = $this->quizService->submitQuiz($request->user(), $quizId, $validated['answers']);
        
        return response()->json([
            'message' => 'Quiz submitted successfully',
            'score' => $result['score'],
            'total' => $result['total'],
            'submission' => $result['submission']
        ], 201);
    }
}
