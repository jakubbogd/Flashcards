<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settings extends Model
{
    use HasFactory;
    
    protected $fillable = ['dark_mode'];

    protected $casts = [
        'dark_mode' => 'boolean',
    ];
}
