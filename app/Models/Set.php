<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Set extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'folder_id',
    ];

    protected $withCount = ['flashcards'];

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
