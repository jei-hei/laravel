<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Scholarship;
use App\Models\AuditLog;

class BatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    protected function checkAdmin()
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            abort(response()->json(['success' => false, 'message' => 'Forbidden'], 403));
        }
    }

    /**
     * Store a new batch for the given scholarship (route-model binding).
     *
     * Route: POST /api/scholarships/{scholarship}/batches
     */
    public function store(Request $request, Scholarship $scholarship)
    {
        $this->checkAdmin();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
        ]);

        // generate name if not provided: "scholarshipId.sequence" (e.g., "1.1")
        if (!$request->filled('name')) {
            $nextSeq = $scholarship->batches()->count() + 1;
            $name = $scholarship->id . '.' . $nextSeq;
        } else {
            $name = $request->input('name');
        }

        // use provided year or fallback to sentinel (0)
        $year = $request->input('year', 0);

        $batch = $scholarship->batches()->create([
            'name' => $name,
            'year' => $year,
            'created_by' => auth()->id(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created batch '.$batch->name,
        ]);

        return response()->json(['message' => 'Batch created', 'batch' => $batch], 201);
    }
}
