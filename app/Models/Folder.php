<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Folder extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function sets()
    {
        return $this->hasMany(Set::class);
    }

    public function flashcards()
    {
        return $this->hasManyThrough(Flashcard::class, Set::class);
    }
}
