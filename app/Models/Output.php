<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    /**
     * Get the user that owns the JoinedClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
