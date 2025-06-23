<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'group',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Quan hệ với roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Quan hệ với users (quyền riêng lẻ)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission')
                    ->withPivot('type')
                    ->withTimestamps();
    }

    /**
     * Scope để lọc quyền đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope để lọc theo nhóm
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Lấy tất cả nhóm quyền
     */
    public static function getGroups()
    {
        return [
            'events' => 'Quản lý sự kiện',
            'users' => 'Quản lý người dùng',
            'checklists' => 'Quản lý công việc',
            'ai_suggestions' => 'Gợi ý AI',
            'system' => 'Hệ thống',
        ];
    }

    /**
     * Lấy tên hiển thị nhóm
     */
    public function getGroupDisplayAttribute()
    {
        $groups = self::getGroups();
        return $groups[$this->group] ?? $this->group;
    }
}
