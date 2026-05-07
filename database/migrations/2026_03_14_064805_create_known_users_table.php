<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('known_users', function (Blueprint $table) {
            $table->id();
            $table->string('official_identifier', 11)->unique();
            $table->string('official_identifier_method', 20);
            $table->string('first_name', 15);
            $table->string('last_name', 15);
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('known_users');
    }
};
