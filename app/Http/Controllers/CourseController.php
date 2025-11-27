<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Batch;

class CourseController extends Controller
{
    public function indexByBatch(Batch $batch)
    {
        return response()->json($batch->courses);
    }

    public function store(Request $request, Batch $batch)
    {
        $request->validate(['name'=>'required|string']);
        $course = $batch->courses()->create(['name'=>$request->name]);
        return response()->json($course, 201);
    }

    public function update(Request $request, Course $course)
    {
        $request->validate(['name'=>'required|string']);
        $course->update($request->only('name'));
        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(['message'=>'deleted']);
    }
}

