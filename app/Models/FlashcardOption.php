<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Flashcard;

class FlashcardOption extends Model
{
    protected $fillable = ['flashcard_id', 'text', 'is_correct'];
    protected $hidden = ['flashcard_id'];
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class);
    }
}
