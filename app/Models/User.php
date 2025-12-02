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
        'name',
        'email',
        'password',
        'role',
        'campus',
        'student_id',
        'lrn',
        'lrn_hash',
    ];

    // Hide sensitive fields from JSON output
    protected $hidden = [
        'password',
        'remember_token',
        'lrn',
        'lrn_hash',
    ];

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

    /**
     * When setting the LRN, also set lrn_hash.
     * - If a bcrypt hash is provided, store it as-is in lrn_hash.
     * - If a plain LRN is provided, hash it and store in lrn_hash.
     * - Still keep the plaintext lrn column (temporary) if you need it during migration.
     */
    public function setLrnAttribute($value)
    {
        $val = $value ? trim($value) : null;
        $this->attributes['lrn'] = $val;

        if ($val === null) {
            $this->attributes['lrn_hash'] = null;
            return;
        }

        // If value looks like a bcrypt hash, store it directly
        if (is_string($val) && strlen($val) === 60 && str_starts_with($val, '$2y$')) {
            $this->attributes['lrn_hash'] = $val;
            return;
        }

        // Otherwise hash the plain LRN
        $this->attributes['lrn_hash'] = Hash::make($val);
    }

    /**
     * Optional explicit setter for lrn_hash if you want to assign a precomputed hash.
     * This avoids double-hashing if you pass a hash directly.
     */
    public function setLrnHashAttribute($value)
    {
        if ($value === null) {
            $this->attributes['lrn_hash'] = null;
            return;
        }

        $val = trim($value);

        if (is_string($val) && strlen($val) === 60 && str_starts_with($val, '$2y$')) {
            $this->attributes['lrn_hash'] = $val;
        } else {
            $this->attributes['lrn_hash'] = Hash::make($val);
        }
    }

    // Hardened password mutator to avoid double-hashing
    public function setPasswordAttribute($password)
    {
        if (is_string($password) && strlen($password) === 60 && str_starts_with($password, '$2y$')) {
            $this->attributes['password'] = $password; // already a bcrypt hash
        } elseif ($password === null) {
            $this->attributes['password'] = null;
        } else {
            $this->attributes['password'] = Hash::make($password);
        }
    }
}
