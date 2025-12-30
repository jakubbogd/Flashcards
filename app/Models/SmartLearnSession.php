<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmartLearnSession extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'total',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(SmartLearnQuestion::class);
    }
}
