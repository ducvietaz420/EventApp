<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Model quản lý checklist sự kiện
 * Lưu trữ các công việc cần thực hiện trong sự kiện
 */
class Checklist extends Model
{
    use HasFactory;
    
    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'event_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'due_date',
        'reminder_date',
        'assigned_to',
        'estimated_cost',
        'actual_cost',
        'notes',
        'attachments',
        'is_template',
        'template_event_type',
        'sort_order',
        'completed_at',
        'completed_by',
        'requires_approval',
        'approval_status',
        'approved_by',
        'approved_at'
    ];
    
    /**
     * Chuyển đổi kiểu dữ liệu cho các trường
     */
    protected $casts = [
        'due_date' => 'date',
        'reminder_date' => 'date',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'attachments' => 'array',
        'is_template' => 'boolean',
        'requires_approval' => 'boolean',
        'sort_order' => 'integer'
    ];
    
    /**
     * Quan hệ với Event (nhiều checklist thuộc về một event)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Kiểm tra xem task có bị trễ không
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'completed') {
            return false;
        }
        
        return $this->due_date && $this->due_date->isPast();
    }
    
    /**
     * Kiểm tra xem có cần nhắc nhở không
     */
    public function getNeedsReminderAttribute(): bool
    {
        if ($this->status === 'completed' || !$this->reminder_date) {
            return false;
        }
        
        return $this->reminder_date->isToday() || $this->reminder_date->isPast();
    }
    
    /**
     * Tính số ngày còn lại đến deadline
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date || $this->status === 'completed') {
            return null;
        }
        
        return now()->diffInDays($this->due_date, false);
    }
    
    /**
     * Lấy màu sắc ưu tiên cho UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }
    
    /**
     * Lấy màu sắc trạng thái cho UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'on_hold' => 'orange',
            default => 'gray'
        };
    }
    
    /**
     * Tính chênh lệch chi phí
     */
    public function getCostVarianceAttribute(): float
    {
        if (!$this->actual_cost || !$this->estimated_cost) {
            return 0;
        }
        
        return $this->actual_cost - $this->estimated_cost;
    }
    
    /**
     * Scope để lọc các task đã hoàn thành
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope để lọc các task chưa hoàn thành
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope để lọc các task có độ ưu tiên cao
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
    
    /**
     * Scope để lọc các task quá hạn
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }
    
    /**
     * Scope để lọc theo danh mục
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
    
    /**
     * Scope để lọc theo trạng thái
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope để lọc theo độ ưu tiên
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }
    
    /**
     * Scope để lấy các task cần phê duyệt
     */
    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true)
                    ->where('approval_status', 'pending');
    }
}
