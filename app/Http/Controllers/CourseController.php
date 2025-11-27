<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Course;
use App\Models\AuditLog;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','role:admin']);
    }

    public function store(Request $request, Batch $batch)
    {
        $request->validate(['name'=>'required|string']);

        $course = $batch->courses()->create($request->only('name'));
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'Added course '.$course->name.' to batch '.$batch->name]);

        return response()->json(['message'=>'Course added','course'=>$course],201);
    }
}
