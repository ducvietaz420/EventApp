<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Tạo bảng events để quản lý thông tin sự kiện tổng thể
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên sự kiện
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->enum('type', ['wedding', 'conference', 'exhibition', 'party', 'corporate', 'other']); // Loại sự kiện
            $table->enum('status', ['planning', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('planning'); // Trạng thái
            $table->date('event_date'); // Ngày diễn ra sự kiện
            $table->time('start_time')->nullable(); // Giờ bắt đầu
            $table->time('end_time')->nullable(); // Giờ kết thúc
            $table->string('venue')->nullable(); // Địa điểm
            $table->text('venue_address')->nullable(); // Địa chỉ chi tiết
            $table->integer('expected_guests')->default(0); // Số khách dự kiến
            $table->decimal('budget', 15, 2)->default(0); // Ngân sách dự kiến
            $table->decimal('actual_cost', 15, 2)->default(0); // Chi phí thực tế
            $table->string('client_name')->nullable(); // Tên khách hàng
            $table->string('client_phone')->nullable(); // SĐT khách hàng
            $table->string('client_email')->nullable(); // Email khách hàng
            $table->json('requirements')->nullable(); // Yêu cầu đặc biệt (JSON)
            $table->text('notes')->nullable(); // Ghi chú
            $table->date('deadline_design')->nullable(); // Deadline thiết kế
            $table->date('deadline_booking')->nullable(); // Deadline đặt dịch vụ
            $table->date('deadline_final')->nullable(); // Deadline hoàn thiện
            $table->timestamps();
            
            // Indexes để tối ưu truy vấn
            $table->index(['event_date', 'status']);
            $table->index('type');
            $table->index('client_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
