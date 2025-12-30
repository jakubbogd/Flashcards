<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartSmartLearnRequest;
use App\Http\Requests\AnswerSmartLearnRequest;
use App\Models\SmartLearnSession;
use App\Models\Flashcard;
use App\Services\SmartLearnService;
use App\Services\SmartLearnAnswerService;
use App\Services\MotivationService;

class SmartLearnController extends Controller
{
    public function __construct(
        protected SmartLearnService $service,
        protected SmartLearnAnswerService $answerService,
        protected MotivationService $motivationService
    ) {}

    public function start(StartSmartLearnRequest $request)
    {
        $flashcards = Flashcard::whereIn(
            'set_id',
            $request->validated()['set_ids']
        )->get()->all();

        $session = $this->service->createSession(
            $flashcards,
            $request->validated()['count']
        );

        return response()->json(['uuid' => $session->uuid], 201);
    }

    public function show(string $uuid)
    {
        return response()->json(
            SmartLearnSession::where('uuid', $uuid)
                ->with(['questions.flashcard.options'])
                ->firstOrFail()
        );
    }

    public function answer(
        AnswerSmartLearnRequest $request,
        string $uuid,
        int $order
    ) {
        $session = SmartLearnSession::where('uuid', $uuid)->firstOrFail();

        $question = $session->questions()
            ->where('order', $order)
            ->firstOrFail();

        abort_if($question->answered_at, 409);

        $isCorrect = $this->answerService->answer(
            $session,
            $question,
            $request->validated()
        );

        return response()->json([
            'is_correct' => $isCorrect,
            'message' => $isCorrect
                ? '✅ Dobrze! Tak właśnie się uczysz.'
                : '❌ Spokojnie, to normalne – lecisz dalej.',
        ]);
    }
}
