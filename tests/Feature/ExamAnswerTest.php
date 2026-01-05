<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Exam;
use App\Models\Flashcard;
use App\Models\ExamQuestion;

class ExamAnswerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_answer_marks_question_correct()
    {
        // Tworzymy egzamin
        $exam = Exam::factory()->create();

        // Tworzymy pytania i upewniamy się, że answered_at jest null
        $questions = ExamQuestion::factory()
            ->count(5)
            ->for($exam)
            ->create(['answered_at' => null]);

        $question = $questions->first();

        // Wywołanie endpointu odpowiedzi
        $response = $this->postJson("/exams/{$exam->uuid}/answer/{$question->order}", [
            'is_correct' => true,
        ]);

        $response->assertStatus(200)
                ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('exam_questions', [
            'order' => $question->order,
            'is_correct' => true,
        ]);

    }
}
