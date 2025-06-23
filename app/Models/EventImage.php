<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model quản lý ảnh của sự kiện
 * Chứa ảnh nghiệm thu và thiết kế
 */
class EventImage extends Model
{
    use HasFactory;

    /**
     * Các trường có thể được gán giá trị hàng loạt
     */
    protected $fillable = [
        'event_id',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'image_type', // 'nghiem_thu' hoặc 'thiet_ke'
        'category', // Phân loại chi tiết: backdrop, led, san-khau, standee, etc.
        'description'
    ];

    /**
     * Các trường được cast sang kiểu dữ liệu cụ thể
     */
    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Quan hệ với bảng events
     * Một ảnh thuộc về một sự kiện
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Lấy URL đầy đủ của file
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Lấy kích thước file dưới dạng con người có thể đọc
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Lấy loại ảnh hiển thị
     */
    public function getImageTypeDisplayAttribute(): string
    {
        return $this->image_type === 'nghiem_thu' ? 'Ảnh Nghiệm Thu' : 'Ảnh Thiết Kế';
    }

    /**
     * Kiểm tra xem có phải ảnh nghiệm thu không
     */
    public function isNghiemThu(): bool
    {
        return $this->image_type === 'nghiem_thu';
    }

    /**
     * Kiểm tra xem có phải ảnh thiết kế không
     */
    public function isThietKe(): bool
    {
        return $this->image_type === 'thiet_ke';
    }

    /**
     * Scope để lọc ảnh nghiệm thu
     */
    public function scopeNghiemThu($query)
    {
        return $query->where('image_type', 'nghiem_thu');
    }

    /**
     * Scope để lọc ảnh thiết kế
     */
    public function scopeThietKe($query)
    {
        return $query->where('image_type', 'thiet_ke');
    }
} 