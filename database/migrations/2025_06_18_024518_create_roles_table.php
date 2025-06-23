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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, manager, user
            $table->string('display_name'); // Quản trị viên, Quản lý, Người dùng
            $table->string('description')->nullable(); // Mô tả vai trò
            $table->string('color')->default('#007bff'); // Màu hiển thị badge
            $table->integer('level')->default(1); // Cấp độ quyền (số càng cao quyền càng lớn)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
