<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('swot_analyses', function (Blueprint $table) {
            $table->id();

            // ربط مع الأفكار
            $table->foreignId('idea_id')
                  ->constrained('ideas')
                  ->onDelete('cascade');

            // SWOT (نخزنهم كـ JSON Arrays)
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('opportunities')->nullable();
            $table->json('threats')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swot_analyses');
    }
};
