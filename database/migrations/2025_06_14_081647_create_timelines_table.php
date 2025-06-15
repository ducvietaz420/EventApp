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
        Schema::create('timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Liên kết với sự kiện
            $table->string('title'); // Tiêu đề hoạt động
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->dateTime('start_time'); // Thời gian bắt đầu
            $table->dateTime('end_time'); // Thời gian kết thúc
            $table->string('location')->nullable(); // Địa điểm cụ thể
            $table->string('responsible_person')->nullable(); // Người phụ trách
            $table->string('contact_info')->nullable(); // Thông tin liên hệ
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending'); // Trạng thái
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium'); // Mức độ ưu tiên
            $table->decimal('estimated_cost', 15, 2)->nullable(); // Chi phí ước tính
            $table->text('requirements')->nullable(); // Yêu cầu đặc biệt
            $table->text('notes')->nullable(); // Ghi chú
            $table->json('attachments')->nullable(); // File đính kèm (JSON array)
            $table->boolean('is_milestone')->default(false); // Đánh dấu mốc quan trọng
            $table->string('google_calendar_event_id')->nullable(); // ID sự kiện Google Calendar
            $table->integer('sort_order')->default(0); // Thứ tự sắp xếp
            $table->timestamps();
            
            // Indexes để tối ưu truy vấn
            $table->index(['event_id', 'start_time']);
            $table->index(['status']);
            $table->index(['priority']);
            $table->index(['is_milestone']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timelines');
    }
};
