<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_id')->constrained('ideas')->onDelete('cascade');

            $table->string('predicted_category')->nullable();
        $table->float('confidence')->nullable();
        $table->boolean('is_ambiguous')->default(false);
        $table->json('top_k')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('analyses');
    }
};
