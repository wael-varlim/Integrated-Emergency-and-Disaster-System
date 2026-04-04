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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('owner_role');
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('notification_id')->nullable()->constrained('notifications');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
