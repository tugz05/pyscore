<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'activity_id',
        'section_id',
        'code',
        'feedback',
        'score',
    ];
}
