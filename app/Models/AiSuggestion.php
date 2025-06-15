<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model quản lý gợi ý từ AI
 * Lưu trữ các đề xuất và gợi ý từ hệ thống AI
 */
class AiSuggestion extends Model
{
    use HasFactory;
    
    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'event_id',
        'suggestion_type',
        'title',
        'content',
        'details',
        'input_parameters',
        'ai_model',
        'confidence_score',
        'status',
        'user_feedback',
        'rating',
        'is_favorite',
        'tags',
        'implementation_notes',
        'estimated_cost',
        'related_suppliers',
        'reviewed_at',
        'reviewed_by'
    ];
    
    /**
     * Chuyển đổi kiểu dữ liệu cho các trường
     */
    protected $casts = [
        'details' => 'array',
        'input_parameters' => 'array',
        'confidence_score' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'tags' => 'array',
        'related_suppliers' => 'array',
        'is_favorite' => 'boolean',
        'rating' => 'integer',
        'reviewed_at' => 'datetime'
    ];
    
    /**
     * Quan hệ với Event (nhiều AI suggestion thuộc về một event)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Kiểm tra xem gợi ý có độ tin cậy cao không
     */
    public function getIsHighConfidenceAttribute(): bool
    {
        return $this->confidence_score >= 0.8;
    }
    
    /**
     * Lấy màu sắc trạng thái cho UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'gray',
            'accepted' => 'green',
            'rejected' => 'red',
            'implemented' => 'blue',
            'under_review' => 'orange',
            default => 'gray'
        };
    }
    
    /**
     * Lấy màu sắc độ tin cậy cho UI
     */
    public function getConfidenceColorAttribute(): string
    {
        if ($this->confidence_score >= 0.8) {
            return 'green';
        } elseif ($this->confidence_score >= 0.6) {
            return 'yellow';
        } elseif ($this->confidence_score >= 0.4) {
            return 'orange';
        } else {
            return 'red';
        }
    }
    
    /**
     * Lấy text mô tả độ tin cậy
     */
    public function getConfidenceTextAttribute(): string
    {
        if ($this->confidence_score >= 0.8) {
            return 'Rất cao';
        } elseif ($this->confidence_score >= 0.6) {
            return 'Cao';
        } elseif ($this->confidence_score >= 0.4) {
            return 'Trung bình';
        } else {
            return 'Thấp';
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
        
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
    
    /**
     * Scope để lọc theo loại gợi ý
     */
    public function scopeBySuggestionType($query, string $type)
    {
        return $query->where('suggestion_type', $type);
    }
    
    /**
     * Scope để lọc theo trạng thái
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope để lấy các gợi ý có độ tin cậy cao
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 0.8);
    }
    
    /**
     * Scope để lấy các gợi ý được yêu thích
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }
    
    /**
     * Scope để lấy các gợi ý đã được chấp nhận
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }
    
    /**
     * Scope để lấy các gợi ý đang chờ xem xét
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    /**
     * Scope để lấy các gợi ý đã được triển khai
     */
    public function scopeImplemented($query)
    {
        return $query->where('status', 'implemented');
    }
    
    /**
     * Scope để sắp xếp theo độ tin cậy cao nhất
     */
    public function scopeOrderByConfidence($query, string $direction = 'desc')
    {
        return $query->orderBy('confidence_score', $direction);
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
     * Scope để lọc theo model AI
     */
    public function scopeByAiModel($query, string $model)
    {
        return $query->where('ai_model', $model);
    }
}
