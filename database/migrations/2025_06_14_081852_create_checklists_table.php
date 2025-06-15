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
        Schema::create('checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Liên kết với sự kiện
            $table->string('title'); // Tiêu đề công việc
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->enum('category', ['planning', 'booking', 'preparation', 'execution', 'cleanup', 'follow_up']); // Danh mục
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium'); // Mức độ ưu tiên
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'overdue'])->default('pending'); // Trạng thái
            $table->date('due_date')->nullable(); // Ngày đến hạn
            $table->dateTime('reminder_date')->nullable(); // Ngày nhắc nhở
            $table->string('assigned_to')->nullable(); // Người được giao
            $table->decimal('estimated_cost', 15, 2)->nullable(); // Chi phí ước tính
            $table->decimal('actual_cost', 15, 2)->nullable(); // Chi phí thực tế
            $table->text('notes')->nullable(); // Ghi chú
            $table->json('attachments')->nullable(); // File đính kèm (JSON array)
            $table->boolean('is_template')->default(false); // Là mẫu checklist
            $table->string('event_type')->nullable(); // Loại sự kiện (cho template)
            $table->integer('sort_order')->default(0); // Thứ tự sắp xếp
            $table->dateTime('completed_at')->nullable(); // Thời gian hoàn thành
            $table->string('completed_by')->nullable(); // Người hoàn thành
            $table->boolean('requires_approval')->default(false); // Cần phê duyệt
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->nullable(); // Trạng thái phê duyệt
            $table->string('approved_by')->nullable(); // Người phê duyệt
            $table->dateTime('approved_at')->nullable(); // Thời gian phê duyệt
            $table->timestamps();
            
            // Indexes để tối ưu truy vấn
            $table->index(['event_id', 'status']);
            $table->index(['category']);
            $table->index(['priority']);
            $table->index(['due_date']);
            $table->index(['is_template', 'event_type']);
            $table->index(['sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
