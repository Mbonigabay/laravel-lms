<?php

namespace App\Http\Controllers;

use App\DTOs\Requests\AddQuestionRequestDTO;
use App\DTOs\Requests\CreateQuizRequestDTO;
use App\DTOs\Requests\SubmitQuizRequestDTO;
use App\Http\Requests\Quiz\AddQuestionRequest;
use App\Http\Requests\Quiz\CreateQuizRequest;
use App\Http\Requests\Quiz\SubmitQuizRequest;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\QuizService;
use App\Traits\ApiResponse;

class QuizController extends Controller
{
    use ApiResponse;

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
     *
     *      @OA\Parameter(name="id", description="Course ID", required=true, in="path", @OA\Schema(type="integer")),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"title"},
     *
     *              @OA\Property(property="title", type="string", example="PHP Basics Quiz")
     *          )
     *      ),
     *
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function storeQuiz(CreateQuizRequest $request, string $courseId)
    {
        $dto = CreateQuizRequestDTO::fromArray($request->validated());
        $responseDto = $this->quizService->createQuiz($courseId, $dto);

        return $this->successResponse($responseDto->toArray(), 'Quiz created successfully', 201);
    }

    /**
     * @OA\Post(
     *      path="/api/quizzes/{id}/questions",
     *      operationId="storeQuestion",
     *      tags={"Quizzes"},
     *      summary="Add a question to a quiz (Teacher only)",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(name="id", description="Quiz ID", required=true, in="path", @OA\Schema(type="integer")),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"question","options","answer"},
     *
     *              @OA\Property(property="question", type="string", example="What does PHP stand for?"),
     *              @OA\Property(property="options", type="array", @OA\Items(type="string"), example={"Python rules", "PHP: Hypertext Preprocessor"}),
     *              @OA\Property(property="answer", type="string", example="PHP: Hypertext Preprocessor")
     *          )
     *      ),
     *
     *      @OA\Response(response=201, description="Successful operation"),
     *      @OA\Response(response=422, description="Validation error"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function storeQuestion(AddQuestionRequest $request, string $quizId)
    {
        $validated = $request->validated();

        if (! in_array($validated['answer'], $validated['options'])) {
            return $this->errorResponse('Answer must be one of the options.', 422);
        }

        $dto = AddQuestionRequestDTO::fromArray($validated);
        $responseDto = $this->quizService->addQuestion($quizId, $dto);

        return $this->successResponse($responseDto->toArray(), 'Question added successfully', 201);
    }

    /**
     * @OA\Post(
     *      path="/api/quizzes/{id}/submit",
     *      operationId="submitQuiz",
     *      tags={"Quizzes"},
     *      summary="Submit quiz answers (Student only)",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(name="id", description="Quiz ID", required=true, in="path", @OA\Schema(type="integer")),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"answers"},
     *
     *              @OA\Property(property="answers", type="object", example={"1": "PHP: Hypertext Preprocessor"})
     *          )
     *      ),
     *
     *      @OA\Response(response=201, description="Successfully submitted"),
     *      @OA\Response(response=404, description="Not found")
     * )
     */
    public function submitQuiz(SubmitQuizRequest $request, string $quizId)
    {
        $dto = SubmitQuizRequestDTO::fromArray($request->validated());
        $result = $this->quizService->submitQuiz($request->user(), $quizId, $dto);

        return $this->successResponse([
            'score' => $result['score'],
            'total' => $result['total'],
            'submission' => $result['submission'],
        ], 'Quiz submitted successfully', 201);
    }
}
