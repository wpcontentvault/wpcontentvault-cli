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
        Schema::create('category_localizations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('category_id')
                ->references('id')
                ->on('categories');
            $table->foreignId('locale_id')
                ->references('id')
                ->on('locales');

            $table->integer('external_id')->nullable();
            $table->string('name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_localizations');
    }
};
