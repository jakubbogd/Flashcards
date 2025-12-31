<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlashcardRequest;
use App\Http\Requests\UpdateFlashcardRequest;
use App\Http\Requests\UpdateFlashcardLearnedRequest;
use App\Models\Flashcard;
use App\Models\FlashcardOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Set;
use App\Services\FlashcardService;

class FlashcardController extends Controller
{
    protected FlashcardService $flashcardService;

    public function __construct(FlashcardService $flashcardService)
    {
        $this->flashcardService = $flashcardService;
    }

    public function index(Set $set)
    {
        return $set->flashcards()->with('options')->latest()->get();
    }

    public function store(StoreFlashcardRequest $request, Set $set)
    {
        $validated = $request->validated();

        $type = $this->flashcardService
            ->determineType($validated['question']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('flashcards', 'public');
        }
        $flashcard = Flashcard::create([
            'set_id' => $set->id,
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'notes' => $validated['notes'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'type' => $type,
        ]);

        $this->flashcardService->generateOptions($flashcard);

        return response()->json(
            $flashcard->load('options'),
            201
        );
    }

    public function update(UpdateFlashcardRequest $request, Set $set, Flashcard $flashcard) {
        $validated = $request->validated();

        if ($flashcard->question !== $validated['question'] || $flashcard->answer !== $validated['answer']) {
            $flashcard->update($validated);
            $type = $this->flashcardService->determineType($validated['question']);
            $flashcard->update(['type' => $type]);
        }
        if (array_key_exists('notes',$validated) && $flashcard->notes !== $validated['notes']) {
            $flashcard->update(['notes' => $validated['notes']]);
        }
        return $flashcard->load('options');
    }

    public function updateLearned(UpdateFlashcardLearnedRequest $request, Flashcard $flashcard)
    {
        $flashcard->update($request->validated());
        return response()->json($flashcard->load('options'));
    }

    public function destroy(Set $set, Flashcard $flashcard)
    {
        $flashcard->delete();
        return response()->json(null, 204);
    }

    public function deleteImage(Flashcard $flashcard)
    {
        Storage::disk('public')->delete($flashcard->image_path);
        $flashcard->image_path = null;
        $flashcard->save();
    }

    public function updateImage(Request $request,Flashcard $flashcard)
    {
        if ($flashcard->image_path) {
            Storage::disk('public')->delete($flashcard->image_path);
        }
        $flashcard->image_path = $request->file('image')->store('flashcards', 'public');
        $flashcard->save();

        return $flashcard;
    }
}