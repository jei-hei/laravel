<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholarship;
use App\Models\AuditLog;



class ScholarshipController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','role:admin'])->except(['index','show']);
    }

    public function index() {
        return response()->json(Scholarship::with('batches.courses')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['title'=>'required','description'=>'nullable']);
        $scholarship = Scholarship::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'created_by'=>auth()->id()
        ]);

        AuditLog::create(['user_id'=>auth()->id(),'action'=>'Created scholarship '.$scholarship->title]);
        return response()->json(['message'=>'Scholarship created','scholarship'=>$scholarship],201);
    }

    public function show(Scholarship $scholarship){
        return response()->json($scholarship->load('batches.courses'));
    }

    public function update(Request $request, Scholarship $scholarship){
        $request->validate(['title'=>'required','description'=>'nullable']);
        $scholarship->update($request->only('title','description'));
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'Updated scholarship '.$scholarship->title]);
        return response()->json(['message'=>'Updated','scholarship'=>$scholarship]);
    }

    public function destroy(Scholarship $scholarship){
        $title = $scholarship->title;
        $scholarship->delete();
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'Deleted scholarship '.$title]);
        return response()->json(['message'=>'Deleted']);
    }
}
