<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
Schema::create('batches', function(Blueprint $table){
    $table->id();
    $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
    $table->timestamps();
});




    }

    public function down()
    {
        Schema::dropIfExists('batches');
    }
};
