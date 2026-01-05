<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Flashcard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_exam_with_correct_fields()
    {
        $exam = Exam::factory()->create();

        $this->assertNotNull($exam->id);
        $this->assertNotNull($exam->uuid);
        $this->assertIsString($exam->sets);
        $this->assertContains($exam->difficulty, ['easy', 'normal', 'hard']);
        $this->assertGreaterThan(0, $exam->total_questions);
        $this->assertGreaterThan(0, $exam->time_limit);
        $this->assertNotNull($exam->started_at);
        $this->assertNull($exam->finished_at);
    }

    #[Test]
    public function it_returns_exam_by_uuid()
    {
        // Stwórz egzamin za pomocą factory
        $exam = Exam::factory()->create([
            'sets' => 'test1 - abc',
            'difficulty' => 'normal',
            'total_questions' => 10,
            'time_limit' => 1200,
        ]);

        $response = $this->getJson("/exams/{$exam->uuid}");

        $response->assertStatus(200)
                 ->assertJson([
                     'uuid' => (string) $exam->uuid,
                     'sets' => 'test1 - abc',
                     'difficulty' => 'normal',
                     'total' => 10,
                     'time_limit' => 1200,
                 ]);

        $this->assertArrayHasKey('started_at', $response->json());
        $this->assertArrayHasKey('finished_at', $response->json());
    }

    #[Test]
    public function it_returns_404_for_nonexistent_uuid()
    {
        $response = $this->getJson("/exams/nonexistent-uuid");
        $response->assertStatus(404);
    }

    #[Test]
    public function test_exam_creation_with_flashcards()
    {
        $flashcards = Flashcard::factory()->count(5)->create();
        $sets = $flashcards->pluck('set.name')->unique()->implode(' - ');

        $exam = Exam::factory()->create([
            'sets' => $sets,
            'total_questions' => 5,
            'time_limit' => 900,
            'difficulty' => 'easy',
        ]);

        // powiązanie pytań
        foreach ($flashcards as $i => $card) {
            $exam->questions()->create([
                'flashcard_id' => $card->id,
                'order' => $i + 1,
            ]);
        }


        $this->assertCount(10, $exam->questions);
        $this->assertEquals(5, $exam->total_questions);

    }

    #[Test]
    public function test_exam_response_contains_percent()
    {
        $exam = Exam::factory()->create(['total_questions' => 4]);
        $flashcards = Flashcard::factory()->count(4)->create();

        foreach ($flashcards as $i => $card) {
            $exam->questions()->create([
                'flashcard_id' => $card->id,
                'order' => $i + 1,
                'is_correct' => $i % 2 === 0,
            ]);
        }

        $response = $this->getJson("/exams/{$exam->uuid}");
        $response->assertStatus(200)
                ->assertJsonStructure(['uuid', 'sets', 'difficulty', 'total', 'time_limit', 'percent']);
    }


}
