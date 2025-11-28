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

            // نصوص تحليل الذكاء الاصطناعي
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('report')->nullable();

            // تقرير PDF (اختياري)
            $table->string('pdf_path')->nullable();
            $table->string('report_type')->default('full');
            $table->string('storage_disk')->default('local');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('analyses');
    }
};
