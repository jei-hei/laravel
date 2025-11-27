<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = ['scholarship_id', 'user_id', 'batch_id', 'course_id', 'status', 'message'];

    public function scholarship() {
        return $this->belongsTo(Scholarship::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function batch() {
        return $this->belongsTo(Batch::class);
    }

    public function course() {
        return $this->belongsTo(Course::class);
    }
}
