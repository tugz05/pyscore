<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'classlist_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function classlist()
    {
        return $this->belongsTo(Classlist::class);
    }
}
