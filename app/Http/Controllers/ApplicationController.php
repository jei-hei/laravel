<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\AuditLog;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Student can submit application
    public function store(Request $request)
    {
        if (!auth()->user() || auth()->user()->role !== 'student') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
            'batch_id' => 'required|exists:batches,id'
        ]);

        $application = Application::create([
            'user_id' => auth()->id(),
            'scholarship_id' => $request->scholarship_id,
            'batch_id' => $request->batch_id,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Submitted application '.$application->id
        ]);

        return response()->json(['message'=>'Application submitted', 'application'=>$application], 201);
    }

    // Admin can update status
    public function updateStatus(Request $request, Application $application)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        $application->update(['status' => $request->status]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated application '.$application->id.' status to '.$application->status
        ]);

        return response()->json(['message'=>'Application status updated', 'application'=>$application]);
    }
}
