<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartExamRequest;
use App\Http\Requests\AnswerExamRequest;
use App\Models\Exam;
use App\Services\ExamService;
use Illuminate\Support\Facades\Auth;
use App\Services\StreakService;
use App\Services\MotivationService;


class ExamController extends Controller
{
    protected ExamService $exam;
    protected StreakService $streak;
    protected MotivationService $motivation;

    public function __construct(
        ExamService $exam,
        StreakService $streak,
        MotivationService $motivation
    ) {
        $this->exam = $exam;
        $this->streak = $streak;
        $this->motivation = $motivation;
    }

    public function start(StartExamRequest $request)
    {
        $exam = $this->exam->start(
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

        $this->streak->markToday();

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
            'message' => $this->motivation->message($exam->percent),
            'streak' => $this->streak->current(),
        ]);
    }

    public function answer(AnswerExamRequest $request, string $uuid, int $order)
    {   
        $exam = Exam::where('uuid', $uuid)->firstOrFail();

        $this->exam->autoFinishIfTimeExceeded($exam);
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

        return response()->json(['ok' => true]);
    }

    public function index()
    {
        return Exam::where('user_id', Auth::id())->latest()
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
