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
        Schema::table('event_images', function (Blueprint $table) {
            $table->string('category')->nullable()->after('image_type')->comment('Phân loại chi tiết: backdrop, led, san-khau, standee, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_images', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
