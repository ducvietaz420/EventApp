<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Các action được phép
     */
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_EXPORT = 'export';
    const ACTION_UPLOAD = 'upload';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_VIEW = 'view';
    const ACTION_STATUS_CHANGE = 'status_change';

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ polymorphic với model được tác động
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Lấy tên hiển thị của action
     */
    public function getActionDisplayAttribute(): string
    {
        return match($this->action) {
            self::ACTION_CREATE => 'Tạo mới',
            self::ACTION_UPDATE => 'Cập nhật',
            self::ACTION_DELETE => 'Xóa',
            self::ACTION_LOGIN => 'Đăng nhập',
            self::ACTION_LOGOUT => 'Đăng xuất',
            self::ACTION_EXPORT => 'Xuất dữ liệu',
            self::ACTION_UPLOAD => 'Tải lên',
            self::ACTION_DOWNLOAD => 'Tải xuống',
            self::ACTION_VIEW => 'Xem',
            self::ACTION_STATUS_CHANGE => 'Thay đổi trạng thái',
            default => 'Không xác định'
        };
    }

    /**
     * Lấy icon cho action
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action) {
            self::ACTION_CREATE => 'fas fa-plus text-success',
            self::ACTION_UPDATE => 'fas fa-edit text-primary',
            self::ACTION_DELETE => 'fas fa-trash text-danger',
            self::ACTION_LOGIN => 'fas fa-sign-in-alt text-success',
            self::ACTION_LOGOUT => 'fas fa-sign-out-alt text-warning',
            self::ACTION_EXPORT => 'fas fa-file-export text-info',
            self::ACTION_UPLOAD => 'fas fa-upload text-info',
            self::ACTION_DOWNLOAD => 'fas fa-download text-info',
            self::ACTION_VIEW => 'fas fa-eye text-secondary',
            self::ACTION_STATUS_CHANGE => 'fas fa-toggle-on text-warning',
            default => 'fas fa-question text-muted'
        };
    }

    /**
     * Lấy CSS class cho action
     */
    public function getActionClassAttribute(): string
    {
        return match($this->action) {
            self::ACTION_CREATE => 'success',
            self::ACTION_UPDATE => 'primary',
            self::ACTION_DELETE => 'danger',
            self::ACTION_LOGIN => 'success',
            self::ACTION_LOGOUT => 'warning',
            self::ACTION_EXPORT, self::ACTION_UPLOAD, self::ACTION_DOWNLOAD => 'info',
            self::ACTION_VIEW => 'secondary',
            self::ACTION_STATUS_CHANGE => 'warning',
            default => 'light'
        };
    }

    /**
     * Scope để lọc theo user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope để lọc theo action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope để lọc theo model
     */
    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query = $query->where('model_id', $modelId);
        }

        return $query;
    }

    /**
     * Scope để lọc theo khoảng thời gian
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Phương thức static để log hoạt động
     */
    public static function log(string $action, string $description, array $data = [])
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return null;
        }

        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $data['model_type'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'description' => $description,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
