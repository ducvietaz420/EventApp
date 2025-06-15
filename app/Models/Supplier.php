<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model quản lý nhà cung cấp
 * Lưu trữ thông tin về các nhà cung cấp dịch vụ
 */
class Supplier extends Model
{
    use HasFactory;
    
    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'name',
        'company_name',
        'type',
        'description',
        'contact_person',
        'phone',
        'email',
        'address',
        'website',
        'social_media',
        'min_budget',
        'max_budget',
        'rating',
        'total_reviews',
        'specialties',
        'service_areas',
        'status',
        'notes',
        'portfolio',
        'certifications',
        'is_preferred',
        'is_verified',
        'last_worked_date'
    ];
    
    /**
     * Chuyển đổi kiểu dữ liệu cho các trường
     */
    protected $casts = [
        'social_media' => 'array',
        'service_areas' => 'array',
        'portfolio' => 'array',
        'certifications' => 'array',
        'min_budget' => 'decimal:2',
        'max_budget' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'is_verified' => 'boolean',
        'is_preferred' => 'boolean',
        'last_worked_date' => 'date'
    ];
    
    /**
     * Quan hệ với Budget (một supplier có thể có nhiều budget)
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
    
    /**
     * Quan hệ với Event thông qua bảng trung gian
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_supplier')
                    ->withPivot(['service_type', 'contract_amount', 'contract_date', 'status'])
                    ->withTimestamps();
    }
    
    /**
     * Tính toán tổng số sự kiện đã tham gia
     */
    public function getTotalEventsAttribute(): int
    {
        return $this->events()->count();
    }
    
    /**
     * Tính toán tổng doanh thu từ supplier này
     */
    public function getTotalRevenueAttribute(): float
    {
        return $this->budgets()->sum('actual_cost') ?? 0;
    }
    
    /**
     * Kiểm tra xem supplier có khả dụng không
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'active';
    }
    
    /**
     * Lấy đánh giá trung bình dạng sao
     */
    public function getStarRatingAttribute(): string
    {
        $stars = round($this->rating);
        return str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
    }
    
    /**
     * Scope để lọc theo loại dịch vụ
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope để lọc theo danh mục (alias cho byType)
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('type', $category);
    }
    
    /**
     * Scope để lấy các supplier đã được xác minh
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
    
    /**
     * Scope để lấy các supplier ưu tiên
     */
    public function scopePreferred($query)
    {
        return $query->where('is_preferred', true);
    }
    
    /**
     * Scope để lấy các supplier khả dụng
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope để sắp xếp theo đánh giá cao nhất
     */
    public function scopeOrderByRating($query, string $direction = 'desc')
    {
        return $query->orderBy('rating', $direction);
    }
    
    /**
     * Scope để tìm kiếm theo tên hoặc dịch vụ
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('specialties', 'like', "%{$term}%");
        });
    }
    
    /**
     * Scope để lọc theo khoảng giá
     */
    public function scopeByPriceRange($query, float $minPrice = null, float $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('min_budget', '>=', $minPrice);
        }
        
        if ($maxPrice !== null) {
            $query->where('max_budget', '<=', $maxPrice);
        }
        
        return $query;
    }
}
