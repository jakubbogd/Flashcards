<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmartLearnQuestion extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'smart_learn_session_id',
        'flashcard_id',
        'order',
        'selected_option_id',
        'is_correct',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(SmartLearnSession::class, 'smart_learn_session_id');
    }

    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class);
    }
}
