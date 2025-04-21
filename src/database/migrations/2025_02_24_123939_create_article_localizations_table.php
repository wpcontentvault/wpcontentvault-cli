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
        Schema::create('article_localizations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('article_id')
                ->references('id')
                ->on('articles');
            $table->foreignId('locale_id')
                ->references('id')
                ->on('locales');
            $table->boolean('is_original');
            $table->integer('external_id');
            $table->string('url');
            $table->string('title');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_localizations');
    }
};
