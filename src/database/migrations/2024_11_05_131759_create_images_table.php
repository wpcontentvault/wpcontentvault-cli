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
        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('article_id')
                ->references('id')
                ->on('articles')
                ->cascadeOnDelete();
            $table->text('path');
            $table->string('hash');
            $table->string('external_id')->nullable();
            $table->string('external_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->boolean('is_stale')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
