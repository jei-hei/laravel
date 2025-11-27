<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
Schema::create('batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
    $table->string('batch_number'); // e.g. "1.1"
    $table->timestamps();
});


    }

    public function down()
    {
        Schema::dropIfExists('batches');
    }
};
