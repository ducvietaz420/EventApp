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
        Schema::create('event_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('role')->nullable(); // Vai trò của supplier trong sự kiện
            $table->decimal('contract_value', 15, 2)->nullable(); // Giá trị hợp đồng
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->date('contract_date')->nullable(); // Ngày ký hợp đồng
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();
            
            // Unique constraint để tránh duplicate
            $table->unique(['event_id', 'supplier_id']);
            
            // Indexes
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_supplier');
    }
};
