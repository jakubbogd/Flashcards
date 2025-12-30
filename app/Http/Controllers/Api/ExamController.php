<?php
// app/Http/Controllers/Api/ExamController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartExamRequest;
use App\Http\Requests\AnswerExamRequest;
use App\Models\Exam;
use App\Services\ExamService;
use App\Services\StreakService;
use App\Services\MotivationService;


class ExamController extends Controller
{
    protected ExamService $examService;
    protected StreakService $streakService;
    protected MotivationService $motivationService;

    public function __construct(
        ExamService $examService,
        StreakService $streakService,
        MotivationService $motivationService
    ) {
        $this->examService = $examService;
        $this->streakService = $streakService;
        $this->motivationService = $motivationService;
    }

    public function start(StartExamRequest $request)
    {
        $exam = $this->examService->start(
            $request->validated()['set_ids'],
            $request->validated()['difficulty']
        );

        return response()->json([
            'uuid' => (string) $exam->uuid,
        ], 201);
    }

    public function show(string $uuid)
    {
        $exam = Exam::where('uuid', $uuid)
            ->with(['questions.flashcard.options'])
            ->firstOrFail();

        $this->streakService->markToday();

        return response()->json([
            'uuid' => (string) $exam->uuid,
            'started_at' => $exam->started_at,
            'finished_at' => $exam->finished_at,
            'score' => $exam->score,
            'sets' => $exam->sets,
            'difficulty' => $exam->difficulty,
            'total' => $exam->total_questions,
            'percent' => $exam->percent,
            'questions' => $exam->questions,
            'time_limit' => $exam->time_limit,
            'message' => $this->motivationService->exam($exam->percent),
            'streak' => $this->streakService->current(),
        ]);
    }

    public function answer(AnswerExamRequest $request, string $uuid, int $order)
    {
        
        $exam = Exam::where('uuid', $uuid)->firstOrFail();

        $this->examService->autoFinishIfTimeExceeded($exam);
        abort_if($exam->finished_at, 409, 'Exam finished');

        $question = $exam->questions()
            ->where('order', $order)
            ->firstOrFail();

        abort_if($question->answered_at, 409, 'Pytanie już zostało udzielone');
        
        $data = $request->validate([
            'is_correct' => ['required', 'boolean'],
        ]);

        $question->update([
            'is_correct' => $data['is_correct'],
            'answered_at' => now(),
        ]);

        if ($exam->questions()->whereNull('answered_at')->count() === 0) {
            $exam->update(['finished_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }

    public function index()
    {
        return Exam::latest()
            ->withCount([
                'questions as correct_answers' => fn ($q) =>
                    $q->where('is_correct', true)
            ])
            ->get()
            ->map(fn ($exam) => [
                'uuid' => (string) $exam->uuid,
                'sets' => $exam->sets,
                'difficulty' => $exam->difficulty,
                'score' => $exam->correct_answers,
                'total' => min($exam->total_questions, $exam->questions->count()),
                'percent' => $exam->percent,
                'finished_at' => $exam->finished_at,
                'started_at' => $exam->started_at,
            ]);
    }
}
