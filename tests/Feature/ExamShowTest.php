<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Exam;
use App\Models\Flashcard;
use App\Models\ExamQuestion;

class ExamShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_exam_show_returns_full_payload()
    {
        $exam = Exam::factory()->create([
            'sets' => 'test1 - abc',
            'difficulty' => 'normal',
            'total_questions' => 5,
            'time_limit' => 1200,
            'started_at' => now(),
        ]);

        $flashcards = Flashcard::factory()->count(5)->create();

        foreach ($flashcards as $i => $flashcard) {
            ExamQuestion::factory()->create([
                'exam_id' => $exam->id,
                'flashcard_id' => $flashcard->id,
                'order' => $i + 1,
            ]);
        }

        $response = $this->getJson("/api/exams/{$exam->uuid}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'uuid',
                'started_at',
                'finished_at',
                'score',
                'sets',
                'difficulty',
                'total',
                'percent',
                'questions',
                'time_limit',
                'message',
                'streak',
            ]);
    }
}
