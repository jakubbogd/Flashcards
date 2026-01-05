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
        'user_id'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(SmartLearnQuestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
