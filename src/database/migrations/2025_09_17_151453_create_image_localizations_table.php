<?php

declare(strict_types=1);

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
        Schema::create('image_localizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('article_id')
                ->references('id')
                ->on('articles')
                ->cascadeOnDelete();
            $table->foreignUuid('image_id')
                ->references('id')
                ->on('images')
                ->cascadeOnDelete();
            $table->foreignId('locale_id')
                ->references('id')
                ->on('locales')
                ->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('external_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_localizations');
    }
};
