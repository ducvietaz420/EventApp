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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên nhà cung cấp
            $table->string('company_name')->nullable(); // Tên công ty
            $table->enum('type', ['catering', 'decoration', 'photography', 'venue', 'entertainment', 'transportation', 'flowers', 'equipment', 'other']); // Loại dịch vụ
            $table->text('description')->nullable(); // Mô tả dịch vụ
            $table->string('contact_person')->nullable(); // Người liên hệ
            $table->string('phone')->nullable(); // Số điện thoại
            $table->string('email')->nullable(); // Email
            $table->text('address')->nullable(); // Địa chỉ
            $table->string('website')->nullable(); // Website
            $table->json('social_media')->nullable(); // Mạng xã hội (JSON)
            $table->decimal('min_budget', 15, 2)->nullable(); // Ngân sách tối thiểu
            $table->decimal('max_budget', 15, 2)->nullable(); // Ngân sách tối đa
            $table->decimal('rating', 3, 2)->default(0); // Đánh giá (0-10)
            $table->integer('total_reviews')->default(0); // Tổng số đánh giá
            $table->text('specialties')->nullable(); // Chuyên môn
            $table->json('service_areas')->nullable(); // Khu vực phục vụ (JSON array)
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active'); // Trạng thái
            $table->text('notes')->nullable(); // Ghi chú nội bộ
            $table->json('portfolio')->nullable(); // Portfolio/hình ảnh (JSON array)
            $table->json('certifications')->nullable(); // Chứng chỉ (JSON array)
            $table->boolean('is_preferred')->default(false); // Nhà cung cấp ưu tiên
            $table->boolean('is_verified')->default(false); // Đã xác minh
            $table->date('last_worked_date')->nullable(); // Lần cuối làm việc
            $table->timestamps();
            
            // Indexes để tối ưu truy vấn
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['rating']);
            $table->index(['is_preferred']);
            $table->index(['is_verified']);
            $table->index(['min_budget', 'max_budget']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
