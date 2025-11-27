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
        $this->middleware(['auth:sanctum','role:admin']);
    }

    public function store(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'name'=>'required|string',
            'year'=>'required|integer'
        ]);

        $batch = $scholarship->batches()->create($request->only('name','year'));
        AuditLog::create(['user_id'=>auth()->id(),'action'=>'Added batch '.$batch->name.' to scholarship '.$scholarship->title]);

        return response()->json(['message'=>'Batch added','batch'=>$batch],201);
    }
}
