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
        Schema::table('timelines', function (Blueprint $table) {
            $table->integer('estimated_duration')->nullable()->after('notes')->comment('Thời lượng dự kiến (phút)');
            $table->integer('actual_duration')->nullable()->after('estimated_duration')->comment('Thời lượng thực tế (phút)');
            $table->text('completion_notes')->nullable()->after('actual_duration')->comment('Ghi chú hoàn thành');
            $table->timestamp('completed_at')->nullable()->after('completion_notes')->comment('Thời gian hoàn thành');
            $table->string('completed_by')->nullable()->after('completed_at')->comment('Người hoàn thành');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timelines', function (Blueprint $table) {
            $table->dropColumn([
                'estimated_duration',
                'actual_duration', 
                'completion_notes',
                'completed_at',
                'completed_by'
            ]);
        });
    }
};
