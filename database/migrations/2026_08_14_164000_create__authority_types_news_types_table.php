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
        Schema::create('authority_types_news_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('authority_type_id')->constrained('authority_types')->cascadeOnDelete();
            $table->foreignId('news_type_id')->constrained('news_types')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authority_types_news_types');
    }
};
