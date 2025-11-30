<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'campus', 'student_id', 'lrn'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'string',
    ];

    // Relationships
    public function applications()   { return $this->hasMany(Application::class); }
    public function loginHistories() { return $this->hasMany(LoginHistory::class); }
    public function auditLogs()      { return $this->hasMany(AuditLog::class); }

    
    public function setCampusAttribute($value)
    {
        $this->attributes['campus'] = $value ? trim($value) : null;
    }

    // Hardened password mutator to avoid double-hashing
    public function setPasswordAttribute($password)
    {
        if (is_string($password) && strlen($password) === 60 && str_starts_with($password, '$2y$')) {
            $this->attributes['password'] = $password; // already a bcrypt hash
        } else {
            $this->attributes['password'] = Hash::make($password);
        }
    }
}
