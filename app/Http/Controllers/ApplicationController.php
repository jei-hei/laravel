<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Scholarship;

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
            'uploaded_file'  => 'nullable|string',
        ]);

        $application = Application::create([
            'user_id'        => auth()->id(),
            'scholarship_id' => $request->scholarship_id,
            'status'         => 'pending',
            'uploaded_file'  => $request->uploaded_file,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action'  => 'Submitted application '.$application->id.' to scholarship '.$request->scholarship_id,
        ]);

        return response()->json(['message' => 'Application submitted', 'application' => $application], 201);
    }

    // Admin can update status
    public function updateStatus(Request $request, Application $application)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $application->update(['status' => $request->status]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action'  => 'Updated application '.$application->id.' status to '.$application->status,
        ]);

        return response()->json(['message' => 'Application status updated', 'application' => $application]);
    }

    // Admin: list applicants for a scholarship
    public function listApplicants(Request $request, Scholarship $scholarship)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $applications = $scholarship->applications()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($applications);
    }

    // Student: view my applications
    public function myApplications(Request $request)
    {
        $apps = Application::with('scholarship')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json($apps);
    }
}
