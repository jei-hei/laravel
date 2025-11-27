<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['scholarship_id','batch_number'];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}

