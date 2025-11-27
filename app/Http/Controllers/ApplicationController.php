<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Course;
use App\Models\Scholarship;

class ApplicationController extends Controller
{
    // student apply
    public function store(Request $request)
    {
        $request->validate([
            'scholarship_id'=>'required|exists:scholarships,id',
            'batch_id'=>'required|exists:batches,id',
            'course_id'=>'required|exists:courses,id',
            'message'=>'nullable|string',
        ]);

        $user = $request->user();
        $application = Application::create([
            'scholarship_id'=>$request->scholarship_id,
            'user_id'=>$user->id,
            'batch_id'=>$request->batch_id,
            'course_id'=>$request->course_id,
            'message'=>$request->message,
            'status'=>'pending',
        ]);

        return response()->json($application, 201);
    }

    // admin view apps for a course
    public function indexByCourse(Course $course)
    {
        $apps = $course->applications()->with('user')->get();
        return response()->json($apps);
    }

    // approve/reject
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate(['status'=>'required|in:pending,approved,rejected']);
        $application->status = $request->status;
        $application->save();
        return response()->json($application);
    }

    // student's own
    public function myApplications(Request $request)
    {
        $user = $request->user();
        $apps = Application::where('user_id',$user->id)->with('scholarship','batch','course')->get();
        return response()->json($apps);
    }

    // optional: index all (admin)
    public function index()
    {
        return response()->json(Application::with('user','scholarship','batch','course')->get());
    }
}
