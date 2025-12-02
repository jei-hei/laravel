<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;
use App\Models\AuditLog;

class ScholarshipController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index','show']);
    }

    public function index()
    {
        // Load applications and the user who applied for each application
        $scholarships = Scholarship::with('applications.user')->get();

        return response()->json(['success' => true, 'scholarships' => $scholarships]);
    }

    public function show(Scholarship $scholarship)
    {
        // Load applications and the user who applied for each application
        return response()->json(['success' => true, 'scholarship' => $scholarship->load('applications.user')]);
    }

    // Helper to check if the authenticated user is admin
    protected function checkAdmin()
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
    }

    public function store(Request $request)
    {
        if ($resp = $this->checkAdmin()) {
            return $resp;
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $scholarship = Scholarship::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id()
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => sprintf('Created scholarship %s', $scholarship->title)
        ]);

        return response()->json(['success' => true, 'message' => 'Scholarship created', 'scholarship' => $scholarship], 201);
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        if ($resp = $this->checkAdmin()) {
            return $resp;
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $scholarship->update($request->only('title','description'));

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => sprintf('Updated scholarship %s', $scholarship->title)
        ]);

        return response()->json(['success' => true, 'message' => 'Scholarship updated', 'scholarship' => $scholarship]);
    }

    public function destroy(Scholarship $scholarship)
    {
        if ($resp = $this->checkAdmin()) {
            return $resp;
        }

        $title = $scholarship->title;
        $scholarship->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => sprintf('Deleted scholarship %s', $title)
        ]);

        return response()->json(['success' => true, 'message' => 'Scholarship deleted']);
    }
}
