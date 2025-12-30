<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'uuid',
        'difficulty',
        'total_questions',
        'started_at',
        'finished_at',
        'time_limit',
        'sets',
        'last_seen_at',
        'times_shown',
        'times_correct'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'last_seen_at'=>'datetime'
    ];

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    public function getScoreAttribute(): int
    {
        return $this->questions()->where('is_correct', true)->count();
    }

    public function getPercentAttribute(): int
    {
        return (int) round(($this->score / min($this->total_questions, count($this->questions))) * 100);
    }

    public function isFinished(): bool
    {
        return !is_null($this->finished_at);
    }
}
