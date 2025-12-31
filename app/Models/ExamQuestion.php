<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'flashcard_id',
        'order',
        'is_correct',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }


    protected static function booted(): void
    {
        static::updated(function (ExamQuestion $question) {
            $exam = $question->exam;
            if (
                $exam->finished_at === null &&
                $exam->questions()->whereNull('answered_at')->count() === 0
            ) {
                $exam->update(['finished_at' => now()]);
            }
        });

    }
}
