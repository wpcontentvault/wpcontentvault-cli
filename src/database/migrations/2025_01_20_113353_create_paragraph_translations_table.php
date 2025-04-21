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
        Schema::create('paragraph_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('article_id')
                ->references('id')
                ->on('articles')
                ->cascadeOnDelete();
            $table->foreignUuid('paragraph_id')
                ->references('id')
                ->on('paragraphs')
                ->cascadeOnDelete();
            $table->foreignId('locale_id')
                ->references('id')
                ->on('locales')
                ->cascadeOnDelete();

            $table->longText('content')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paragraph_translations');
    }
};
