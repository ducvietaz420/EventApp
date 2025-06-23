<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Quan hệ với users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Quan hệ với permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Scope để lọc role đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope để sắp xếp theo level
     */
    public function scopeOrderByLevel($query, $direction = 'desc')
    {
        return $query->orderBy('level', $direction);
    }

    /**
     * Kiểm tra role có quyền cụ thể không
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('permissions.name', $permission)->exists();
        }
        
        if ($permission instanceof Permission) {
            return $this->permissions()->where('permissions.id', $permission->id)->exists();
        }
        
        return false;
    }

    /**
     * Cấp quyền cho role
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission && !$this->hasPermission($permission)) {
            $this->permissions()->attach($permission);
        }
        
        return $this;
    }

    /**
     * Thu hồi quyền từ role
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission) {
            $this->permissions()->detach($permission);
        }
        
        return $this;
    }

    /**
     * Đồng bộ quyền cho role
     */
    public function syncPermissions($permissions)
    {
        $permissionIds = collect($permissions)->map(function ($permission) {
            if (is_string($permission)) {
                return Permission::where('name', $permission)->first()?->id;
            }
            return $permission instanceof Permission ? $permission->id : $permission;
        })->filter();
        
        $this->permissions()->sync($permissionIds);
        
        return $this;
    }

    /**
     * Lấy tất cả quyền của role
     */
    public function getAllPermissions()
    {
        return $this->permissions()->active()->get();
    }
}
