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
        Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['user', 'admin', 'analyst'])->default('user');
    $table->json('profile')->nullable();
    $table->timestamps();
     $table->string('fcm_token')->nullable()->after('remember_token');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('fcm_token');   });
        Schema::dropIfExists('users');
    }
};
