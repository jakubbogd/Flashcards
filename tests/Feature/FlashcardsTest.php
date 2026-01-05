<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Set;
use App\Models\Flashcard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class FlashcardsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_flashcard()
    {
        $set = Set::factory()->create();

        $response = $this->postJson("/{$set->id}/flashcards", [
            'question' => 'What is PHP?',
            'answer' => 'Programming language',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('flashcards', [
            'question' => 'What is PHP?',
            'set_id' => $set->id,
        ]);
    }

    #[Test]
    public function it_updates_flashcard()
    {
        $card = Flashcard::factory()->create();

        $this->putJson("/{$card->set_id}/flashcards/{$card->id}", [
            'question' => 'Updated?',
            'answer' => 'Yes',
        ])->assertOk();

        $this->assertDatabaseHas('flashcards', [
            'id' => $card->id,
            'question' => 'Updated?',
        ]);
    }

    #[Test]
    public function it_deletes_flashcard()
    {
        $card = Flashcard::factory()->create();

        $this->deleteJson("/{$card->set_id}/flashcards/{$card->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('flashcards', [
            'id' => $card->id,
        ]);
    }

    #[Test]
    public function test_flashcard_can_toggle_learned_status()
    {
        $flashcard = Flashcard::factory()->fresh()->create();

        $this->assertFalse($flashcard->learned);

        $response = $this->putJson("/flashcards/{$flashcard->id}/learned", [
            'learned' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('flashcards', [
            'id' => $flashcard->id,
            'learned' => true,
        ]);
    }

    #[Test]
    public function test_flashcard_deletion_removes_record()
    {
        $flashcard = Flashcard::factory()->create();

        $response = $this->deleteJson("/{$flashcard->set_id}/flashcards/{$flashcard->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('flashcards', ['id' => $flashcard->id]);
    }

}
