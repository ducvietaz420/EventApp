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
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Liên kết với sự kiện
            $table->enum('suggestion_type', ['concept', 'theme', 'color_scheme', 'decoration', 'menu', 'entertainment', 'venue', 'timeline', 'budget', 'other']); // Loại gợi ý
            $table->string('title'); // Tiêu đề gợi ý
            $table->text('content'); // Nội dung gợi ý
            $table->json('details')->nullable(); // Chi tiết gợi ý (JSON)
            $table->json('input_parameters')->nullable(); // Tham số đầu vào (JSON)
            $table->string('ai_model')->default('gemini'); // Model AI sử dụng
            $table->decimal('confidence_score', 5, 2)->nullable(); // Điểm tin cậy (0-100)
            $table->enum('status', ['generated', 'reviewed', 'accepted', 'rejected', 'implemented'])->default('generated'); // Trạng thái
            $table->text('user_feedback')->nullable(); // Phản hồi từ người dùng
            $table->integer('rating')->nullable(); // Đánh giá (1-5 sao)
            $table->boolean('is_favorite')->default(false); // Đánh dấu yêu thích
            $table->json('tags')->nullable(); // Tags/nhãn (JSON array)
            $table->text('implementation_notes')->nullable(); // Ghi chú triển khai
            $table->decimal('estimated_cost', 15, 2)->nullable(); // Chi phí ước tính
            $table->json('related_suppliers')->nullable(); // Nhà cung cấp liên quan (JSON array)
            $table->dateTime('reviewed_at')->nullable(); // Thời gian xem xét
            $table->string('reviewed_by')->nullable(); // Người xem xét
            $table->timestamps();
            
            // Indexes để tối ưu truy vấn
            $table->index(['event_id', 'suggestion_type']);
            $table->index(['status']);
            $table->index(['ai_model']);
            $table->index(['is_favorite']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_suggestions');
    }
};
