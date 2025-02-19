<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classlist extends Model
{
    use HasFactory;

    public $incrementing = false; // Disable auto-incrementing ID
    protected $keyType = 'string'; // Ensure the primary key is treated as a string

    protected $fillable = [
        'id', // Google Classroom-style ID as primary key
        'user_id',
        'section_id',
        'name',
        'academic_year',
    ];

    // Automatically generate Google Classroom-style ID when creating a section
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($section) {
            $section->id = self::generateClassroomCode(); // Generate Google Classroom-style ID
        });
    }

    // Generate a Google Classroom-style code (e.g., abc-defg-hij)
    public static function generateClassroomCode()
    {
        return strtolower(Str::random(3)) . '-' . strtolower(Str::random(4)) . '-' . strtolower(Str::random(3));
    }

    /**
     * Get the section that owns the Classlist
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
    /**
     * Get all of the activities for the Classlist
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
    /**
     * Get the user that owns the Classlist
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get all of the joinedclasses for the Classlist
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function joinedclasses(): HasMany
    {
        return $this->hasMany(JoinedClass::class);
    }


public function instructor()
{
    return $this->belongsTo(User::class, 'user_id',); // User who created the class
}
}
