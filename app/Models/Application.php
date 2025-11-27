<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['user_id','scholarship_id','batch_id','course_id','status','uploaded_file'];
    public function user() { return $this->belongsTo(User::class); }
    public function scholarship() { return $this->belongsTo(Scholarship::class); }
    public function batch() { return $this->belongsTo(Batch::class); }
    public function course() { return $this->belongsTo(Course::class); }
}
