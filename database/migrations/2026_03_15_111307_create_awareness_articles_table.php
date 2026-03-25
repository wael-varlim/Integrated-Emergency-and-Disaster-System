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
        Schema::create('awareness_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('body')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('icon_url');
            $table->foreignId('news_type_id')->constrained('news_types')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awareness_articles');
    }
};
