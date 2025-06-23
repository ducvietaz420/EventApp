<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model quản lý sự kiện
 * Chứa thông tin chi tiết về các sự kiện được tổ chức
 */
class Event extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'venue_address',
        'client_name',
        'client_phone',
        'client_email',
        'expected_guests',
        'budget',
        'actual_cost',
        'requirements',
        'notes',
        'deadline_design',
        'deadline_booking',
        'deadline_final'
    ];

    /**
     * Các trường được cast sang kiểu dữ liệu cụ thể
     */
    protected $casts = [
        'event_date' => 'date',
        'expected_guests' => 'integer',
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'deadline_design' => 'date',
        'deadline_booking' => 'date',
        'deadline_final' => 'date',
        'requirements' => 'array',
        'setup_deadline' => 'date',
        'final_deadline' => 'date'
    ];

    /**
     * Quan hệ với bảng event_images
     * Một sự kiện có nhiều ảnh
     */
    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class);
    }

    /**
     * Quan hệ với bảng checklists
     * Một sự kiện có nhiều checklist
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class);
    }

    /**
     * Quan hệ với bảng ai_suggestions
     * Một sự kiện có nhiều gợi ý AI
     */
    public function aiSuggestions(): HasMany
    {
        return $this->hasMany(AiSuggestion::class);
    }



    /**
     * Quan hệ với bảng suppliers
     * Một sự kiện có nhiều nhà cung cấp (many-to-many)
     */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'event_supplier')
                    ->withPivot(['role', 'contract_value', 'status', 'notes'])
                    ->withTimestamps();
    }



    /**
     * Lấy ảnh nghiệm thu
     */
    public function nghiemThuImages(): HasMany
    {
        return $this->images()->nghiemThu();
    }

    /**
     * Lấy ảnh thiết kế
     */
    public function thietKeImages(): HasMany
    {
        return $this->images()->thietKe();
    }

    /**
     * Tính tổng số ảnh
     */
    public function getTotalImagesAttribute(): int
    {
        return $this->images()->count();
    }

    /**
     * Tính tổng số ảnh nghiệm thu
     */
    public function getTotalNghiemThuImagesAttribute(): int
    {
        return $this->nghiemThuImages()->count();
    }

    /**
     * Tính tổng số ảnh thiết kế
     */
    public function getTotalThietKeImagesAttribute(): int
    {
        return $this->thietKeImages()->count();
    }

    /**
     * Lấy trạng thái tiến độ sự kiện
     */
    public function getProgressStatusAttribute(): string
    {
        $totalTasks = $this->checklists()->count();
        $completedTasks = $this->checklists()->where('status', 'completed')->count();
        
        if ($totalTasks == 0) {
            return 'not_started';
        }
        
        $percentage = ($completedTasks / $totalTasks) * 100;
        
        if ($percentage == 100) {
            return 'completed';
        } elseif ($percentage >= 75) {
            return 'nearly_done';
        } elseif ($percentage >= 50) {
            return 'in_progress';
        } elseif ($percentage > 0) {
            return 'started';
        } else {
            return 'not_started';
        }
    }

    /**
     * Lấy phần trăm tiến độ hoàn thành
     */
    public function getProgressPercentageAttribute(): float
    {
        $totalTasks = $this->checklists()->count();
        if ($totalTasks == 0) {
            return 0;
        }
        
        $completedTasks = $this->checklists()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Lấy số ngày còn lại đến sự kiện
     */
    public function getDaysUntilEventAttribute(): int
    {
        return now()->diffInDays($this->event_date, false);
    }

    /**
     * Kiểm tra xem sự kiện có đang diễn ra không
     */
    public function getIsOngoingAttribute(): bool
    {
        return now()->isSameDay($this->event_date);
    }

    /**
     * Kiểm tra xem sự kiện đã kết thúc chưa
     */
    public function getIsCompletedAttribute(): bool
    {
        return now()->isAfter($this->event_date);
    }

    /**
     * Lấy màu sắc trạng thái cho UI
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning' => 'blue',
            'confirmed' => 'green',
            'in_progress' => 'orange',
            'completed' => 'purple',
            'cancelled' => 'red',
            'postponed' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Lấy tên hiển thị trạng thái bằng tiếng Việt
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'planning' => 'Đang lên kế hoạch',
            'confirmed' => 'Đã xác nhận',
            'in_progress' => 'Đang tiến hành',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'postponed' => 'Hoãn lại',
            default => ucfirst($this->status)
        };
    }

    /**
     * Lấy tên hiển thị loại sự kiện bằng tiếng Việt
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'wedding' => 'Đám cưới',
            'conference' => 'Hội nghị',
            'party' => 'Tiệc',
            'corporate' => 'Doanh nghiệp',
            'exhibition' => 'Triển lãm',
            'workshop' => 'Workshop',
            'seminar' => 'Seminar',
            'other' => 'Khác',
            default => ucfirst($this->type)
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
     * Scope để lọc theo loại sự kiện
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope để lấy các sự kiện sắp tới
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>', now());
    }

    /**
     * Scope để lấy các sự kiện đang diễn ra
     */
    public function scopeOngoing($query)
    {
        return $query->whereDate('event_date', now()->toDateString());
    }

    /**
     * Scope để lấy các sự kiện đã hoàn thành
     */
    public function scopeCompleted($query)
    {
        return $query->where('event_date', '<', now());
    }

    /**
     * Scope để sắp xếp theo ngày sự kiện
     */
    public function scopeOrderByEventDate($query, string $direction = 'asc')
    {
        return $query->orderBy('event_date', $direction);
    }

    /**
     * Scope để tìm kiếm theo tên hoặc mô tả
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('client_name', 'like', "%{$search}%");
        });
    }
}
