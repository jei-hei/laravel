<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = ['title','description','created_by'];

    public function batches() { return $this->hasMany(Batch::class); }
    public function applications() { return $this->hasMany(Application::class); }
}
