<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị danh sách người dùng
     */
    public function index(Request $request)
    {
        $query = User::with('creator');
        
        // Lọc theo vai trò
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Tìm kiếm theo tên hoặc username
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(15);
        
        return view('users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo người dùng mới
     */
    public function create()
    {
        $roles = Role::active()->orderByLevel()->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Lưu người dùng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'role_id' => 'required|exists:roles,id',
            'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])],
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'username.unique' => 'Tên đăng nhập này đã được sử dụng',
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'role.required' => 'Vui lòng chọn vai trò',
            'role_id.required' => 'Vui lòng chọn vai trò chi tiết',
            'status.required' => 'Vui lòng chọn trạng thái',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'created_by' => Auth::id(),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Tài khoản người dùng đã được tạo thành công');
    }

    /**
     * Hiển thị chi tiết người dùng
     */
    public function show(User $user)
    {
        $user->load('creator', 'createdUsers');
        return view('users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa người dùng
     */
    public function edit(User $user)
    {
        $roles = Role::active()->orderByLevel()->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'status' => ['required', Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])],
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'username.unique' => 'Tên đăng nhập này đã được sử dụng',
            'username.regex' => 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã được sử dụng',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'role.required' => 'Vui lòng chọn vai trò',
            'status.required' => 'Vui lòng chọn trạng thái',
        ]);

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ];

        // Chỉ cập nhật mật khẩu nếu có nhập
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'Thông tin người dùng đã được cập nhật');
    }

    /**
     * Xóa người dùng
     */
    public function destroy(User $user)
    {
        // Không cho phép xóa chính mình
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không thể xóa tài khoản của chính mình');
        }

        // Không cho phép xóa admin cuối cùng
        if ($user->isAdmin() && User::admins()->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Không thể xóa admin cuối cùng trong hệ thống');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Đã xóa tài khoản '{$userName}' thành công");
    }

    /**
     * Chuyển đổi trạng thái người dùng (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        // Không cho phép thay đổi trạng thái của chính mình
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Bạn không thể thay đổi trạng thái tài khoản của chính mình');
        }

        // Không cho phép khóa admin cuối cùng
        if ($user->isAdmin() && $user->isActive() && User::admins()->active()->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Không thể khóa admin cuối cùng đang hoạt động trong hệ thống');
        }

        $newStatus = $user->isActive() ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
        $user->update(['status' => $newStatus]);

        $statusText = $newStatus === User::STATUS_ACTIVE ? 'kích hoạt' : 'tạm khóa';
        
        return redirect()->route('users.index')
            ->with('success', "Đã {$statusText} tài khoản '{$user->name}' thành công");
    }

    /**
     * Hiển thị trang quản lý phân quyền cho user
     */
    public function permissions(User $user)
    {
        $user->load('roleModel.permissions', 'permissions');
        $allPermissions = Permission::active()->get()->groupBy('group');
        
        // Tạo mapping permission states cho JavaScript
        $permissionStates = [];
        foreach ($allPermissions->flatten() as $permission) {
            $userPermission = $user->permissions->where('id', $permission->id)->first();
            
            if ($userPermission) {
                // User có quyền riêng lẻ (grant/deny)
                $permissionStates[$permission->id] = $userPermission->pivot->type;
            } else {
                // Dùng từ role hoặc default
                $permissionStates[$permission->id] = $user->hasPermission($permission->name) ? 'grant' : 'default';
            }
        }
        
        \Log::info('Permission states for user ' . $user->id, $permissionStates);
        
        // Debug: Check actual user permissions in database
        $actualUserPermissions = [];
        foreach ($user->permissions as $perm) {
            $actualUserPermissions[$perm->id] = $perm->pivot->type;
        }
        \Log::info('Actual user permissions in database:', $actualUserPermissions);
        
        return view('users.permissions', compact('user', 'allPermissions', 'permissionStates'));
    }

    /**
     * Cập nhật phân quyền cho user
     */
    public function updatePermissions(Request $request, User $user)
    {
        // Debug: Log request data
        \Log::info('Update Permissions Request:', [
            'user_id' => $user->id,
            'all_request' => $request->all(),
            'granted_permissions' => $request->get('granted_permissions', []),
            'denied_permissions' => $request->get('denied_permissions', [])
        ]);

        $request->validate([
            'role_id' => 'nullable|exists:roles,id',
            'granted_permissions' => 'array',
            'granted_permissions.*' => 'exists:permissions,id',
            'denied_permissions' => 'array', 
            'denied_permissions.*' => 'exists:permissions,id',
        ]);

        // Cập nhật role nếu có
        if ($request->has('role_id')) {
            $user->update(['role_id' => $request->role_id]);
        }

        // Xóa tất cả quyền riêng lẻ cũ
        $user->permissions()->detach();

        // Parse permissions từ individual permission fields
        $grantedPermissions = [];
        $deniedPermissions = [];
        
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'permission_') === 0) {
                $permissionId = str_replace('permission_', '', $key);
                
                if ($value === 'grant') {
                    $grantedPermissions[] = $permissionId;
                } elseif ($value === 'deny') {
                    $deniedPermissions[] = $permissionId;
                }
            }
        }
        
        \Log::info('Parsed permissions from fields:', [
            'granted' => $grantedPermissions,
            'denied' => $deniedPermissions
        ]);

        // Cấp quyền riêng lẻ
        foreach ($grantedPermissions as $permissionId) {
            if (Permission::find($permissionId)) {
                $user->permissions()->attach($permissionId, ['type' => 'grant']);
                \Log::info("Granted permission {$permissionId} to user {$user->id}");
            }
        }

        // Từ chối quyền riêng lẻ
        foreach ($deniedPermissions as $permissionId) {
            if (Permission::find($permissionId)) {
                $user->permissions()->attach($permissionId, ['type' => 'deny']);
                \Log::info("Denied permission {$permissionId} to user {$user->id}");
            }
        }

        // Reload user với relationships mới
        $user->load('roleModel.permissions', 'permissions');

        return redirect()->route('users.permissions', $user)
            ->with('success', 'Phân quyền đã được cập nhật thành công');
    }
}
