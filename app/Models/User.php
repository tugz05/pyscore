<?php
namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
use HasApiTokens, HasFactory, Notifiable;
/**
* The attributes that are mass assignable.
*
* @var array<int, string>
*/
protected $fillable = [
'google_id',
'email',
'student_id',
'name',
'avatar',
'account_type',
'email_verified_at',
'password',
'status',
];
/**
* The attributes that should be hidden for serialization.
*
* @var array<int, string>
*/
protected $hidden = [
'password',
'remember_token',
];
/**
* The attributes that should be cast.
*
* @var array<string, string>
*/
protected $casts = [
'email_verified_at' => 'datetime',
'password' => 'hashed',
];
/**
* Get all of the classlists for the User
*
* @return \Illuminate\Database\Eloquent\Relations\HasMany
*/
public function classlists(): HasMany
{
return $this->hasMany(Classlist::class);
}

/**
 * Get all of the archives for the User
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function archives(): HasMany
{
    return $this->hasMany(Archive::class);
}
}
