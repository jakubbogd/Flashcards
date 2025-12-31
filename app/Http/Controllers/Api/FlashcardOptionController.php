<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Flashcard;
use App\Models\FlashcardOption;
use Illuminate\Http\Request;

class FlashcardOptionController extends Controller
{
    public function update(Request $request, FlashcardOption $option)
    {
        $option->update(['text' => $request->text]);
        return $option;
    }

    public function store(Request $request, Flashcard $flashcard)
    {
        $option = FlashcardOption::create([
                'flashcard_id' => $flashcard->id,
                'text' => $request->text,
                'is_correct' =>  false
            ]);
        return $option;
    }
}