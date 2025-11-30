<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Batch;
use App\Models\AuditLog;

class CourseController extends Controller
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

    // List courses for a batch
    public function index(Batch $batch)
    {
        $courses = $batch->courses()->get();
        return response()->json(['courses' => $courses], 200);
    }

    // Create a course inside a batch
    public function store(Request $request, Batch $batch)
    {
        $this->checkAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course = $batch->courses()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'created_by' => auth()->id(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created course '.$course->name.' in batch '.$batch->name,
        ]);

        return response()->json(['message' => 'Course created', 'course' => $course], 201);
    }

    // Show a single course (ensure it belongs to the batch)
    public function show(Batch $batch, Course $course)
    {
        if ($course->batch_id !== $batch->id) {
            return response()->json(['message' => 'Course not found in this batch'], 404);
        }
        return response()->json(['course' => $course], 200);
    }

    // Update a course
    public function update(Request $request, Batch $batch, Course $course)
    {
        $this->checkAdmin();

        if ($course->batch_id !== $batch->id) {
            return response()->json(['message' => 'Course not found in this batch'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->update($request->only(['name','description']));

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated course '.$course->name.' in batch '.$batch->name,
        ]);

        return response()->json(['message' => 'Course updated', 'course' => $course], 200);
    }

    // Delete a course
    public function destroy(Batch $batch, Course $course)
    {
        $this->checkAdmin();

        if ($course->batch_id !== $batch->id) {
            return response()->json(['message' => 'Course not found in this batch'], 404);
        }

        $course->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted course '.$course->name.' from batch '.$batch->name,
        ]);

        return response()->json(['message' => 'Course deleted'], 200);
    }
}
