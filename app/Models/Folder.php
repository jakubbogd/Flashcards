<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sets()
    {
        return $this->hasMany(Set::class);
    }

    public function flashcards()
    {
        return $this->hasManyThrough(Flashcard::class, Set::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (Folder $folder) {
            $folder->sets()->delete();
        });
    }
}
