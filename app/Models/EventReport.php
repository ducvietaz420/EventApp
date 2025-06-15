<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model quản lý báo cáo sự kiện
 * Lưu trữ các báo cáo tổng kết và phân tích sau sự kiện
 */
class EventReport extends Model
{
    use HasFactory;
    
    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'event_id',
        'report_type',
        'title',
        'summary',
        'content',
        'metrics',
        'financial_summary',
        'attendance_data',
        'feedback_summary',
        'lessons_learned',
        'recommendations',
        'attachments',
        'photos',
        'videos',
        'status',
        'visibility',
        'generated_by',
        'reviewed_by',
        'approved_by',
        'published_at',
        'reviewed_at',
        'approved_at',
        'tags',
        'rating',
        'success_score',
        'roi_percentage',
        'cost_variance',
        'timeline_variance',
        'stakeholder_satisfaction',
        'report_title',
        'executive_summary',
        'total_budget',
        'total_spent',
        'budget_variance',
        'total_attendees',
        'expected_attendees',
        'attendance_rate',
        'overall_satisfaction',
        'strengths',
        'weaknesses',
        'improvements',
        'supplier_ratings',
        'timeline_analysis',
        'created_by'
    ];
    
    /**
     * Chuyển đổi kiểu dữ liệu cho các trường
     */
    protected $casts = [
        'metrics' => 'array',
        'financial_summary' => 'array',
        'attendance_data' => 'array',
        'feedback_summary' => 'array',
        'lessons_learned' => 'array',
        'recommendations' => 'array',
        'attachments' => 'array',
        'photos' => 'array',
        'videos' => 'array',
        'tags' => 'array',
        'rating' => 'decimal:1',
        'success_score' => 'decimal:2',
        'roi_percentage' => 'decimal:2',
        'cost_variance' => 'decimal:2',
        'timeline_variance' => 'integer',
        'stakeholder_satisfaction' => 'decimal:2',
        'published_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime'
    ];
    
    /**
     * Quan hệ với Event (nhiều báo cáo thuộc về một event)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Kiểm tra xem báo cáo đã được xuất bản chưa
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' && $this->published_at !== null;
    }
    
    /**
     * Kiểm tra xem báo cáo đã được phê duyệt chưa
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved' || $this->status === 'published';
    }
    
    /**
     * Lấy màu sắc trạng thái cho UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'under_review' => 'orange',
            'approved' => 'green',
            'published' => 'blue',
            'rejected' => 'red',
            'archived' => 'purple',
            default => 'gray'
        };
    }
    
    /**
     * Lấy text mô tả mức độ thành công
     */
    public function getSuccessLevelAttribute(): string
    {
        if ($this->success_score >= 90) {
            return 'Xuất sắc';
        } elseif ($this->success_score >= 80) {
            return 'Tốt';
        } elseif ($this->success_score >= 70) {
            return 'Khá';
        } elseif ($this->success_score >= 60) {
            return 'Trung bình';
        } else {
            return 'Cần cải thiện';
        }
    }
    
    /**
     * Lấy màu sắc mức độ thành công cho UI
     */
    public function getSuccessColorAttribute(): string
    {
        if ($this->success_score >= 80) {
            return 'green';
        } elseif ($this->success_score >= 70) {
            return 'blue';
        } elseif ($this->success_score >= 60) {
            return 'yellow';
        } else {
            return 'red';
        }
    }
    
    /**
     * Lấy text đánh giá ROI
     */
    public function getRoiStatusAttribute(): string
    {
        if ($this->roi_percentage > 20) {
            return 'Rất tốt';
        } elseif ($this->roi_percentage > 10) {
            return 'Tốt';
        } elseif ($this->roi_percentage > 0) {
            return 'Dương';
        } elseif ($this->roi_percentage == 0) {
            return 'Hòa vốn';
        } else {
            return 'Âm';
        }
    }
    
    /**
     * Lấy text đánh giá sao
     */
    public function getStarRatingAttribute(): string
    {
        if (!$this->rating) {
            return 'Chưa đánh giá';
        }
        
        $fullStars = floor($this->rating);
        $halfStar = ($this->rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;
        
        return str_repeat('★', $fullStars) . 
               str_repeat('☆', $halfStar) . 
               str_repeat('☆', $emptyStars);
    }
    
    /**
     * Scope để lọc theo loại báo cáo
     */
    public function scopeByReportType($query, string $type)
    {
        return $query->where('report_type', $type);
    }
    
    /**
     * Scope để lọc theo trạng thái
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope để lấy các báo cáo đã xuất bản
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }
    
    /**
     * Scope để lấy các báo cáo nháp
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }
    
    /**
     * Scope để lấy các báo cáo đang chờ duyệt
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'under_review');
    }
    
    /**
     * Scope để lọc theo mức độ hiển thị
     */
    public function scopeByVisibility($query, string $visibility)
    {
        return $query->where('visibility', $visibility);
    }
    
    /**
     * Scope để lấy báo cáo công khai
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }
    
    /**
     * Scope để sắp xếp theo điểm thành công cao nhất
     */
    public function scopeOrderBySuccess($query, string $direction = 'desc')
    {
        return $query->orderBy('success_score', $direction);
    }
    
    /**
     * Scope để sắp xếp theo ROI cao nhất
     */
    public function scopeOrderByRoi($query, string $direction = 'desc')
    {
        return $query->orderBy('roi_percentage', $direction);
    }
    
    /**
     * Scope để sắp xếp theo đánh giá cao nhất
     */
    public function scopeOrderByRating($query, string $direction = 'desc')
    {
        return $query->orderBy('rating', $direction);
    }
    
    /**
     * Scope để tìm kiếm theo tag
     */
    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }
    
    /**
     * Scope để lọc báo cáo có điểm thành công cao
     */
    public function scopeHighSuccess($query, float $threshold = 80.0)
    {
        return $query->where('success_score', '>=', $threshold);
    }
}
