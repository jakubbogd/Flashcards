<?php

namespace App\Jobs;

use App\Models\Flashcard;
use App\Models\Set;
use App\Services\FlashcardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ImportFlashcardsFromCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $content;
    protected Set $set;
    public $timeout = 1200000;

    public function __construct(array $content, Set $set)
    {
        $this->content = $content;
        $this->set = $set;
    }

    public function handle(FlashcardService $service): void
    {

        foreach ($this->content as $key => $line) {
            if ($key === 0) {
                continue;
            }
            
            [$question, $answer] = explode(';', $line);
            $answer = trim($answer);
            $question = trim($question);
            if (!$question || !$answer) {
                continue;
            }

            $type = $service->determineType($question);

            $flashcard = Flashcard::create([
                'question' => $question,
                'answer'   => $answer,
                'type'     => $type,
                'set_id'   => $this->set->id,
            ]);

            $service->generateOptions($flashcard);
        }
    }
}
