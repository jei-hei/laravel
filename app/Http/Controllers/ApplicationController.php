<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        $request->validate([
            'scholarship_id'=>'required|exists:scholarships,id',
            'batch_id'=>'required|exists:batches,id',
            'course_id'=>'required|exists:courses,id',
        ]);

        $exists = Application::where('user_id',auth()->id())
            ->where('batch_id',$request->batch_id)->first();

        if($exists) return response()->json(['message'=>'Already applied to this batch'],422);

        DB::transaction(function() use($request){
            $application = Application::create([
                'user_id'=>auth()->id(),
                'scholarship_id'=>$request->scholarship_id,
                'batch_id'=>$request->batch_id,
                'course_id'=>$request->course_id,
                'status'=>'ongoing',
            ]);

            AuditLog::create(['user_id'=>auth()->id(),'action'=>'Applied to scholarship ID '.$application->scholarship_id]);
        });

        return response()->json(['message'=>'Application submitted'],201);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate(['status'=>'required|in:approved,declined']);

        DB::transaction(function() use($request,$application){
            $application->update(['status'=>$request->status]);
            AuditLog::create(['user_id'=>auth()->id(),'action'=>'Updated application ID '.$application->id.' status to '.$request->status]);
        });

        return response()->json(['message'=>'Status updated','application'=>$application]);
    }
}
