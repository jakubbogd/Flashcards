<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ImportFlashcardsFromCsv;
use App\Http\Requests\ImportFlashcardsRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Set;
class FlashcardImportController extends Controller
{
    public function store(ImportFlashcardsRequest $request, Set $set)
    {
        $path = $request->file('file')->store('imports');
        $content = explode("\n", Storage::get($path));
        ImportFlashcardsFromCsv::dispatch($content, $set);

        return response()->json([
            'message' => 'Import uruchomiony. Fiszki są przetwarzane w tle.'
        ]);
    }

}
