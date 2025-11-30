<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
Schema::create('applications', function(Blueprint $table){
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
    $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
    $table->foreignId('course_id')->constrained()->cascadeOnDelete();
    $table->enum('status',['pending','approved','rejected'])->default('pending');
    $table->string('uploaded_file')->nullable();
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
};
