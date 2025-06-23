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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('model_type')->nullable(); // Tên model được tác động
            $table->unsignedBigInteger('model_id')->nullable(); // ID của model được tác động
            $table->text('description'); // Mô tả chi tiết hoạt động
            $table->json('old_values')->nullable(); // Giá trị cũ (cho action update)
            $table->json('new_values')->nullable(); // Giá trị mới (cho action update/create)
            $table->string('ip_address')->nullable(); // Địa chỉ IP
            $table->text('user_agent')->nullable(); // User Agent
            $table->timestamps();
            
            // Index để tối ưu query
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
