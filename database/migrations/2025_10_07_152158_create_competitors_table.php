<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_id')->constrained('ideas')->onDelete('cascade');
           // بيانات المنافس
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('region')->nullable();   // city
            $table->string('country')->nullable();
            $table->string('company_size')->nullable(); // 51-200
            $table->string('website')->nullable();

            // درجة التشابه من AI
            $table->float('similarity_score')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitors');
    }
};
