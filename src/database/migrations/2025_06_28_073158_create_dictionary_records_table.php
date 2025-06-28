<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dictionary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')
                ->references('id')
                ->on('locales');
            $table->foreignId('target_id')
                ->references('id')
                ->on('locales');
            $table->string('source');
            $table->string('context')->nullable();
            $table->string('translation');
            $table->json('embedding');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dictionary_records');
    }
};
