<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Add all fields you want to allow mass assignment
    protected $fillable = [
        'name',
        'id_number',   // added
        'lrn',         // added
        'campus',      // added
        'role',
        'email',
        'password',    // optional if used
        
    ];

    protected $hidden = [
        'password',
        'lrn',          // optional: hide LRN
        'remember_token',
    ];


    // Relationships
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
