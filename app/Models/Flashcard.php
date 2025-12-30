<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FlashcardOption;
use App\Models\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flashcard extends Model
{
    use HasFactory;
    
    protected $fillable = ['question', 'answer','type', 'learned','set_id','notes','image_path'];
    protected $casts = [
        'learned' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(FlashcardOption::class);
    }
    public function set()
    {
        return $this->belongsTo(Set::class, 'set_id');
    }
}
