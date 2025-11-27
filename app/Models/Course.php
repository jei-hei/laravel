<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name','batch_id'];
    public function batch() { return $this->belongsTo(Batch::class); }
    public function applications() { return $this->hasMany(Application::class); }
}
