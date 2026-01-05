<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FlashcardOption;
use App\Models\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Services\FlashcardService;

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

    protected static function booted(): void
    {
        static::deleting(function (Flashcard $flashcard) {
            $flashcard->options()->delete();
            if ($flashcard->image_path && Storage::exists($flashcard->image_path)) {
                Storage::delete($flashcard->image_path);
            }
        });

        static::updated(function (Flashcard $flashcard) {
            if ($flashcard->wasChanged(['question', 'answer'])) {
                app(FlashcardService::class)->generateOptions($flashcard);
            }
});

    }
}
