<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật enum cho column suggestion_type
        DB::statement("ALTER TABLE ai_suggestions MODIFY COLUMN suggestion_type ENUM('budget', 'timeline', 'checklist', 'supplier', 'general', 'concept', 'theme', 'color_scheme', 'decoration', 'menu', 'entertainment', 'venue', 'other') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert về enum cũ
        DB::statement("ALTER TABLE ai_suggestions MODIFY COLUMN suggestion_type ENUM('concept', 'theme', 'color_scheme', 'decoration', 'menu', 'entertainment', 'venue', 'timeline', 'budget', 'other') NOT NULL");
    }
};
