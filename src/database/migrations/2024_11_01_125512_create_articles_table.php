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
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('path');
            $table->jsonb('content')->nullable();
            $table->jsonb('text_ids')->default('[]');
            $table->jsonb('image_ids')->default('[]');
            $table->jsonb('keyword_ids')->default('[]');
            $table->string('author')->nullable();
            $table->string('title')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('url')->nullable();
            $table->foreignId('locale_id')
                ->references('id')
                ->on('locales');
            $table->string('external_id')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('modified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
