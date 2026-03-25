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
        Schema::create('media_translations', function (Blueprint $table) {
            $table->id();
            $table->string('languahe_code', 2);
            $table->string('translation')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->foreignId('media_type_id')->constrained('media_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_translations');
    }
};
