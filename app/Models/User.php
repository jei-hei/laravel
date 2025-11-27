<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

   protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'campus',
    'student_id',
    'lrn'
];

    protected $hidden = ['password','remember_token'];

    public function applications() { return $this->hasMany(Application::class); }
    public function loginHistories() { return $this->hasMany(LoginHistory::class); }
    public function auditLogs() { return $this->hasMany(AuditLog::class); }

    public function isAdmin() { return $this->role === 'admin'; }
    public function isStudent() { return $this->role === 'student'; }

    public function setPasswordAttribute($password) { $this->attributes['password'] = Hash::make($password); }
}
