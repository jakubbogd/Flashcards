<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Set;
use App\Models\Flashcard;

class ExamStartTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_exam_can_be_started()
    {
        $set = Set::factory()->create(['name' => 'test1']);
        Flashcard::factory()->count(15)->create(['set_id' => $set->id]);

        $response = $this->postJson('/api/exams/start', [
            'set_ids' => [$set->id],
            'difficulty' => 'easy',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'uuid',
            ]);

        $this->assertDatabaseCount('exams', 1);
        $this->assertDatabaseCount('exam_questions', 5);
    }

    #[Test]
    public function test_exam_start_validation()
    {
        $response = $this->postJson('/api/exams/start', []);

        $response->assertStatus(422);
    }
}
