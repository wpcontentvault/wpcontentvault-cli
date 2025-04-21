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
        Schema::table('articles', function (Blueprint $table) {
            $table->jsonb('paragraph_ids')->default('[]');

            $table->dropColumn('keyword_ids');
            $table->dropColumn('text_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('paragraph_ids');

            $table->jsonb('keyword_ids')->default('[]');
            $table->jsonb('text_ids')->default('[]');
        });
    }
};
