<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Classlist extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'user_id',
        'section_id',
        'name',
        'academic_year',
    ];
    // Automatically generate a Google Classroom-style code when creating a section
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($section) {
            $section->code = self::generateClassroomCode();
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
}
