<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Scholarship;

class BatchController extends Controller
{
    // list batches for scholarship
    public function indexByScholarship(Scholarship $scholarship)
    {
        return response()->json($scholarship->batches()->with('courses')->get());
    }

    public function store(Request $request, Scholarship $scholarship)
    {
        $request->validate(['batch_number'=>'required|string']);
        $batch = $scholarship->batches()->create([
            'batch_number' => $request->batch_number,
        ]);
        return response()->json($batch, 201);
    }

    public function update(Request $request, Batch $batch)
    {
        $request->validate(['batch_number'=>'required|string']);
        $batch->update($request->only('batch_number'));
        return response()->json($batch);
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return response()->json(['message'=>'deleted']);
    }
}

