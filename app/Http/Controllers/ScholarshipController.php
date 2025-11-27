<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;

class ScholarshipController extends Controller
{
    public function index()
    {
        return response()->json(Scholarship::with('batches')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['title'=>'required|string','description'=>'nullable|string']);
        $s = Scholarship::create($request->only('title','description'));
        return response()->json($s, 201);
    }

    public function show(Scholarship $scholarship)
    {
        $scholarship->load('batches.courses'); // eager load
        return response()->json($scholarship);
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $request->validate(['title'=>'required|string','description'=>'nullable|string']);
        $scholarship->update($request->only('title','description'));
        return response()->json($scholarship);
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return response()->json(['message'=>'deleted']);
    }
}

