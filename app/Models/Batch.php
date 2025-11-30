<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    // keep year sentinel for now; remove later if you drop the column
    protected $fillable = ['name','scholarship_id','year','created_by'];

    protected $attributes = [
        'year' => 0,
    ];

    public function scholarship() { return $this->belongsTo(Scholarship::class); }
    public function courses() { return $this->hasMany(Course::class); }
}
