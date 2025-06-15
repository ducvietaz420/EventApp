<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng budgets để quản lý ngân sách và theo dõi chi phí chi tiết
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade'); // Liên kết với sự kiện
            $table->string('category'); // Danh mục chi phí (venue, catering, decoration, etc.)
            $table->string('item_name'); // Tên khoản chi phí
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->decimal('estimated_cost', 12, 2)->default(0); // Chi phí dự kiến
            $table->decimal('actual_cost', 12, 2)->default(0); // Chi phí thực tế
            $table->enum('status', ['planned', 'quoted', 'booked', 'paid', 'cancelled'])->default('planned'); // Trạng thái
            $table->date('due_date')->nullable(); // Ngày đáo hạn thanh toán
            $table->date('paid_date')->nullable(); // Ngày thanh toán thực tế
            $table->string('supplier_name')->nullable(); // Tên nhà cung cấp
            $table->string('supplier_contact')->nullable(); // Thông tin liên hệ nhà cung cấp
            $table->text('notes')->nullable(); // Ghi chú
            $table->json('attachments')->nullable(); // File đính kèm (hóa đơn, báo giá)
            $table->boolean('is_essential')->default(false); // Có phải chi phí thiết yếu không
            $table->integer('priority')->default(5); // Độ ưu tiên (1-10)
            $table->timestamps();
            
            // Indexes để tối ưu truy vấn
            $table->index(['event_id', 'category']);
            $table->index(['status', 'due_date']);
            $table->index('supplier_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
