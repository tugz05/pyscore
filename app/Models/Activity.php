<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'classlist_id',
        'section_id',
        'title',
        'instruction',
        'points',
        'due_date',
        'due_time',
        'accessible_date',
        'accessible_time',
    ];
    /**
     * Get the classlist that owns the Activity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classlist(): BelongsTo
    {
        return $this->belongsTo(Classlist::class, 'classlist_id');
    }
    /**
     * Get the section that owns the Activity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
    /**
     * Get the user that owns the Activity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
