<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Set;
use App\Models\Flashcard;

class ApiContractExamTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_exam_contract()
    {
        $set = Set::factory()->create(['name' => 'A']);
        Flashcard::factory()->count(20)->create(['set_id' => $set->id]);

        $start = $this->postJson('/exams/start', [
            'set_ids' => [$set->id],
            'difficulty' => 'easy',
        ]);

        $start->assertStatus(201)
            ->assertJsonStructure(['uuid']);

        $uuid = $start->json('uuid');

        $show = $this->getJson("/exams/{$uuid}");

        $show->assertStatus(200)
            ->assertJsonStructure([
                'uuid',
                'questions' => [
                    '*' => [
                        'id',
                        'order',
                        'flashcard',
                    ],
                ],
            ]);
    }
}
