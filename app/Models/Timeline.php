<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Model quản lý timeline sự kiện
 * Lưu trữ các mốc thời gian và hoạt động trong sự kiện
 */
class Timeline extends Model
{
    use HasFactory;
    
    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'event_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'responsible_person',
        'status',
        'priority',
        'is_milestone',
        'dependencies',
        'estimated_duration',
        'actual_duration',
        'notes',
        'completion_notes',
        'completed_at',
        'completed_by'
    ];
    
    /**
     * Chuyển đổi kiểu dữ liệu cho các trường
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'completed_at' => 'datetime',
        'is_milestone' => 'boolean',
        'estimated_duration' => 'integer',
        'actual_duration' => 'integer',
        'dependencies' => 'array'
    ];
    
    /**
     * Quan hệ với Event (nhiều timeline thuộc về một event)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Tính toán thời gian còn lại đến khi bắt đầu
     */
    public function getTimeUntilStartAttribute(): ?int
    {
        if (!$this->start_time || $this->start_time->isPast()) {
            return null;
        }
        
        return now()->diffInMinutes($this->start_time);
    }
    
    /**
     * Kiểm tra xem có đang diễn ra không
     */
    public function getIsActiveAttribute(): bool
    {
        $now = now();
        return $this->start_time <= $now && $this->end_time >= $now;
    }
    
    /**
     * Kiểm tra xem có bị trễ không
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'completed') {
            return false;
        }
        
        return $this->end_time && $this->end_time->isPast();
    }
    
    /**
     * Tính toán chênh lệch thời gian thực hiện
     */
    public function getDurationVarianceAttribute(): ?int
    {
        if (!$this->actual_duration || !$this->estimated_duration) {
            return null;
        }
        
        return $this->actual_duration - $this->estimated_duration;
    }
    
    /**
     * Lấy trạng thái màu sắc cho UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'delayed' => 'orange',
            default => 'gray'
        };
    }
    
    /**
     * Scope để lọc theo trạng thái
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope để lấy các milestone
     */
    public function scopeMilestones($query)
    {
        return $query->where('is_milestone', true);
    }
    
    /**
     * Scope để lấy các hoạt động đang diễn ra
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now);
    }
    
    /**
     * Scope để lấy các hoạt động sắp tới
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }
    
    /**
     * Scope để lấy các hoạt động bị trễ
     */
    public function scopeOverdue($query)
    {
        return $query->where('end_time', '<', now())
                    ->where('status', '!=', 'completed');
    }
    
    /**
     * Scope để sắp xếp theo thời gian bắt đầu
     */
    public function scopeOrderByStartTime($query, string $direction = 'asc')
    {
        return $query->orderBy('start_time', $direction);
    }
}
