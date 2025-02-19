<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinedClass extends Model
{
    use HasFactory;
    protected $fillable = [
        'classlist_id',
        'user_id',
        'date_joined',
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

    public function classlist()
    {
        return $this->belongsTo(Classlist::class );
    }
    /**
     * Get the section that owns the JoinedClass
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
