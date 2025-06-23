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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // events.create, events.edit, users.manage
            $table->string('display_name'); // Tạo sự kiện, Chỉnh sửa sự kiện
            $table->string('description')->nullable(); // Mô tả chi tiết quyền
            $table->string('group'); // events, users, checklists, ai_suggestions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
