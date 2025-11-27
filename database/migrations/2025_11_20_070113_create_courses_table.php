<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
Schema::create('courses', function(Blueprint $table){
    $table->id();
    $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->timestamps();
    $table->index('batch_id');
});



    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
