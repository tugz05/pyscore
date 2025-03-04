<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'schedule_from',
        'schedule_to',
        'day'
    ];
    /**
     * Get all of the classlists for the Section
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classlists(): HasMany
    {
        return $this->hasMany(Classlist::class);
    }
}
