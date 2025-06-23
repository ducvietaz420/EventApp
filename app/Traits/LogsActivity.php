<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        // Log khi tạo mới
        static::created(function (Model $model) {
            if (auth()->check()) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => ActivityLog::ACTION_CREATE,
                    'model_type' => get_class($model),
                    'model_id' => $model->getKey(),
                    'description' => 'Tạo mới ' . strtolower(class_basename($model)) . ' #' . $model->getKey(),
                    'new_values' => $model->getAttributes(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        // Log khi cập nhật
        static::updated(function (Model $model) {
            if (auth()->check()) {
                $oldValues = [];
                $newValues = [];
                
                // Lấy các thuộc tính đã thay đổi
                $changes = $model->getChanges();
                $original = $model->getOriginal();
                
                foreach ($changes as $key => $newValue) {
                    if (isset($original[$key])) {
                        $oldValues[$key] = $original[$key];
                        $newValues[$key] = $newValue;
                    }
                }
                
                if (!empty($oldValues) || !empty($newValues)) {
                    // Tạo mô tả chi tiết về thay đổi
                    $descriptions = [];
                    foreach ($changes as $key => $newValue) {
                        $oldValue = $original[$key] ?? 'null';
                        $descriptions[] = "{$key}: '{$oldValue}' → '{$newValue}'";
                    }
                    
                    $description = 'Cập nhật ' . strtolower(class_basename($model)) . ' #' . $model->getKey();
                    if (!empty($descriptions)) {
                        $description .= ' (' . implode(', ', $descriptions) . ')';
                    }

                    ActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => ActivityLog::ACTION_UPDATE,
                        'model_type' => get_class($model),
                        'model_id' => $model->getKey(),
                        'description' => $description,
                        'old_values' => $oldValues,
                        'new_values' => $newValues,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }
        });

        // Log khi xóa
        static::deleted(function (Model $model) {
            if (auth()->check()) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => ActivityLog::ACTION_DELETE,
                    'model_type' => get_class($model),
                    'model_id' => $model->getKey(),
                    'description' => 'Xóa ' . strtolower(class_basename($model)) . ' #' . $model->getKey(),
                    'old_values' => $model->getOriginal(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });
    }

    /**
     * Lấy tên hiển thị của model cho việc logging
     */
    protected function getModelDisplayName(): string
    {
        $className = class_basename($this);
        
        $displayNames = [
            'Event' => 'sự kiện',
            'User' => 'người dùng', 
            'Checklist' => 'công việc',
            'AiSuggestion' => 'gợi ý AI',
            'Budget' => 'ngân sách',
            'Timeline' => 'timeline',
            'Supplier' => 'nhà cung cấp',
            'EventImage' => 'hình ảnh sự kiện',
        ];

        return $displayNames[$className] ?? strtolower($className);
    }

    /**
     * Lấy các thuộc tính quan trọng để log (có thể override trong model)
     */
    protected function getLoggedAttributes(): array
    {
        // Mặc định log tất cả attributes trừ những field nhạy cảm
        $hidden = ['password', 'remember_token', 'created_at', 'updated_at'];
        
        return collect($this->getAttributes())
            ->except($hidden)
            ->toArray();
    }
} 