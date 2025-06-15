<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model quản lý ngân sách sự kiện
 * Lưu trữ thông tin chi tiết về các khoản chi tiêu
 */
class Budget extends Model
{
    use HasFactory;
    
    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'event_id',
        'category',
        'item_name',
        'description',
        'estimated_cost',
        'actual_cost',
        'allocated_date',
        'deadline',
        'notes',
        'supplier_id',
        'payment_status',
        'payment_date',
        'payment_method',
        'invoice_number',
        'receipt_file',
        'is_approved',
        'approved_by',
        'approved_at',
        'priority'
    ];
    
    /**
     * Chuyển đổi kiểu dữ liệu cho các trường
     */
    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'allocated_date' => 'date',
        'deadline' => 'date',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'is_approved' => 'boolean'
    ];
    
    /**
     * Quan hệ với Event (nhiều budget thuộc về một event)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Quan hệ với Supplier (nhiều budget có thể thuộc về một supplier)
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Quan hệ với ExpenseLog (một budget có thể có nhiều expense logs)
     */
    public function expenseLogs()
    {
        return $this->hasMany(ExpenseLog::class)->orderBy('created_at', 'desc');
    }
    
    /**
     * Tính toán chênh lệch giữa chi phí ước tính và thực tế
     */
    public function getVarianceAttribute(): float
    {
        if (!$this->actual_cost || !$this->estimated_cost) {
            return 0;
        }
        
        return $this->actual_cost - $this->estimated_cost;
    }
    
    /**
     * Tính phần trăm chênh lệch so với ước tính
     */
    public function getVariancePercentageAttribute(): float
    {
        if (!$this->estimated_cost || $this->estimated_cost == 0) {
            return 0;
        }
        
        return ($this->variance / $this->estimated_cost) * 100;
    }
    
    /**
     * Kiểm tra xem có vượt ngân sách không
     */
    public function isOverBudget(): bool
    {
        return $this->variance > 0;
    }
    
    /**
     * Lấy tên hiển thị danh mục bằng tiếng Việt
     */
    public function getCategoryDisplayAttribute(): string
    {
        return match($this->category) {
            'venue' => 'Địa điểm',
            'catering' => 'Catering',
            'decoration' => 'Trang trí',
            'equipment' => 'Thiết bị',
            'marketing' => 'Marketing',
            'staff' => 'Nhân sự',
            'transportation' => 'Vận chuyển',
            'other' => 'Khác',
            default => ucfirst($this->category)
        };
    }
    
    /**
     * Scope để lọc theo danh mục
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
    
    /**
     * Scope để lọc theo trạng thái thanh toán
     */
    public function scopeByPaymentStatus($query, string $status)
    {
        return $query->where('payment_status', $status);
    }
    
    /**
     * Scope để lấy các khoản đã được phê duyệt
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
    
    /**
     * Scope để lấy các khoản chưa được phê duyệt
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
}
