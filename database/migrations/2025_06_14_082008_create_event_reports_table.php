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
        Schema::create('event_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Liên kết với sự kiện
            $table->string('report_title'); // Tiêu đề báo cáo
            $table->enum('report_type', ['financial', 'feedback', 'performance', 'comprehensive', 'custom']); // Loại báo cáo
            $table->text('executive_summary')->nullable(); // Tóm tắt điều hành
            $table->json('financial_summary')->nullable(); // Tóm tắt tài chính (JSON)
            $table->decimal('total_budget', 15, 2)->nullable(); // Tổng ngân sách
            $table->decimal('total_spent', 15, 2)->nullable(); // Tổng chi tiêu
            $table->decimal('budget_variance', 15, 2)->nullable(); // Chênh lệch ngân sách
            $table->integer('total_attendees')->nullable(); // Tổng số người tham dự
            $table->integer('expected_attendees')->nullable(); // Số người dự kiến
            $table->decimal('attendance_rate', 5, 2)->nullable(); // Tỷ lệ tham dự (%)
            $table->decimal('overall_satisfaction', 3, 2)->nullable(); // Mức độ hài lòng tổng thể (1-10)
            $table->json('feedback_summary')->nullable(); // Tóm tắt phản hồi (JSON)
            $table->json('strengths')->nullable(); // Điểm mạnh (JSON array)
            $table->json('weaknesses')->nullable(); // Điểm yếu (JSON array)
            $table->json('improvements')->nullable(); // Đề xuất cải thiện (JSON array)
            $table->json('supplier_ratings')->nullable(); // Đánh giá nhà cung cấp (JSON)
            $table->json('timeline_analysis')->nullable(); // Phân tích timeline (JSON)
            $table->text('lessons_learned')->nullable(); // Bài học kinh nghiệm
            $table->text('recommendations')->nullable(); // Khuyến nghị
            $table->json('attachments')->nullable(); // File đính kèm (JSON array)
            $table->json('photos')->nullable(); // Hình ảnh sự kiện (JSON array)
            $table->enum('status', ['draft', 'completed', 'reviewed', 'approved', 'archived'])->default('draft'); // Trạng thái
            $table->string('created_by')->nullable(); // Người tạo báo cáo
            $table->string('reviewed_by')->nullable(); // Người xem xét
            $table->dateTime('reviewed_at')->nullable(); // Thời gian xem xét
            $table->dateTime('published_at')->nullable(); // Thời gian xuất bản
            $table->timestamps();
            
            // Indexes để tối ưu truy vấn
            $table->index(['event_id']);
            $table->index(['report_type']);
            $table->index(['status']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_reports');
    }
};
