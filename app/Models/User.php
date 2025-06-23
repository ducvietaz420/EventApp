<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    /**
     * Các vai trò người dùng
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /**
     * Các trạng thái người dùng
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'role_id',
        'status',
        'last_login_at',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Kiểm tra xem người dùng có phải admin không
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Kiểm tra xem người dùng có đang hoạt động không
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Lấy tên hiển thị vai trò
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Quản trị viên',
            self::ROLE_USER => 'Người dùng',
            default => 'Không xác định'
        };
    }

    /**
     * Lấy tên hiển thị trạng thái
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Hoạt động',
            self::STATUS_INACTIVE => 'Tạm khóa',
            default => 'Không xác định'
        };
    }

    /**
     * Quan hệ với người tạo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Quan hệ với những người dùng được tạo
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Scope để lọc admin
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope để lọc user
     */
    public function scopeUsers($query)
    {
        return $query->where('role', self::ROLE_USER);
    }

    /**
     * Scope để lọc người dùng đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope để lọc người dùng bị khóa
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Quan hệ với role mới
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Quan hệ với permissions riêng lẻ
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission')
                    ->withPivot('type')
                    ->withTimestamps();
    }

    /**
     * Kiểm tra user có quyền cụ thể không
     */
    public function hasPermission($permission)
    {
        $permissionName = is_string($permission) ? $permission : $permission->name;
        
        // Kiểm tra quyền riêng lẻ trước (có thể override role permission)
        $userPermission = $this->permissions()
                              ->where('permissions.name', $permissionName)
                              ->first();

        if ($userPermission) {
            // Nếu quyền bị từ chối, trả về false ngay lập tức
            if ($userPermission->pivot->type === 'deny') {
                return false;
            }
            // Nếu quyền được cấp, trả về true
            if ($userPermission->pivot->type === 'grant') {
                return true;
            }
        }

        // Nếu không có quyền riêng lẻ, kiểm tra quyền từ role
        if ($this->roleModel && $this->roleModel->hasPermission($permission)) {
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra user có bất kỳ quyền nào trong danh sách
     */
    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kiểm tra user có tất cả quyền trong danh sách
     */
    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Cấp quyền riêng lẻ cho user
     */
    public function givePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->syncWithoutDetaching([
                $permission->id => ['type' => 'grant']
            ]);
        }

        return $this;
    }

    /**
     * Từ chối quyền riêng lẻ cho user
     */
    public function denyPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->syncWithoutDetaching([
                $permission->id => ['type' => 'deny']
            ]);
        }

        return $this;
    }

    /**
     * Thu hồi quyền riêng lẻ của user
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
     * Lấy tất cả quyền của user (từ role + quyền riêng lẻ)
     */
    public function getAllPermissions()
    {
        $rolePermissions = collect();
        $userPermissions = collect();

        // Quyền từ role
        if ($this->roleModel) {
            $rolePermissions = $this->roleModel->getAllPermissions();
        }

        // Quyền riêng lẻ được cấp
        $userPermissions = $this->permissions()
                               ->wherePivot('type', 'grant')
                               ->get();

        // Quyền bị từ chối
        $deniedPermissions = $this->permissions()
                                 ->wherePivot('type', 'deny')
                                 ->pluck('permissions.name')
                                 ->toArray();

        // Kết hợp và loại bỏ quyền bị từ chối
        return $rolePermissions->concat($userPermissions)
                              ->filter(function ($permission) use ($deniedPermissions) {
                                  return !in_array($permission->name, $deniedPermissions);
                              })
                              ->unique('id');
    }

    /**
     * Kiểm tra user có vai trò cụ thể không (theo role mới)
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roleModel && $this->roleModel->name === $role;
        }

        if ($role instanceof Role) {
            return $this->role_id === $role->id;
        }

        return false;
    }

    /**
     * Gán role mới cho user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role instanceof Role) {
            $this->update(['role_id' => $role->id]);
        }

        return $this;
    }
}
