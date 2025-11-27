<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['name','scholarship_id','year'];
    public function scholarship() { return $this->belongsTo(Scholarship::class); }
    public function courses() { return $this->hasMany(Course::class); }
}
