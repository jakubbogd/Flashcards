<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyDay extends Model
{
    protected $fillable = ['date', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}