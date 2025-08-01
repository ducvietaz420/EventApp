@extends('layouts.app')

@section('title', 'Phân quyền người dùng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Phân quyền người dùng
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Quản lý người dùng</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></li>
                            <li class="breadcrumb-item active">Phân quyền</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Xem chi tiết
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- User Info -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                Thông tin người dùng
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="avatar-circle-large mx-auto mb-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-3">{{ $user->email }}</p>
                            
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} fs-6">
                                        <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user' }} me-1"></i>
                                        {{ $user->role_display }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }} fs-6">
                                        <i class="fas {{ $user->status === 'active' ? 'fa-check' : 'fa-ban' }} me-1"></i>
                                        {{ $user->status_display }}
                                    </span>
                                </div>
                            </div>

                            @if($user->roleModel)
                                <div class="alert alert-info">
                                    <strong>Vai trò hiện tại:</strong> {{ $user->roleModel->display_name }}
                                    <br><small>{{ $user->roleModel->description }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Current Permissions Summary -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                Tổng quan quyền hiện tại
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $allUserPermissions = $user->getAllPermissions();
                                $grantedPerms = $user->permissions()->wherePivot('type', 'grant')->get();
                                $deniedPerms = $user->permissions()->wherePivot('type', 'deny')->get();
                            @endphp
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="text-primary">
                                        <i class="fas fa-shield-alt fa-2x"></i>
                                        <h6 class="mt-2">{{ $allUserPermissions->count() }}</h6>
                                        <small>Tổng quyền</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-success">
                                        <i class="fas fa-plus-circle fa-2x"></i>
                                        <h6 class="mt-2">{{ $grantedPerms->count() }}</h6>
                                        <small>Được cấp</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-danger">
                                        <i class="fas fa-minus-circle fa-2x"></i>
                                        <h6 class="mt-2">{{ $deniedPerms->count() }}</h6>
                                        <small>Bị từ chối</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Management -->
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('users.permissions.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Role Selection -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-tag me-2"></i>
                                    Vai trò cơ bản
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="role_id" class="form-label">Chọn vai trò</label>
                                        <select class="form-select" id="role_id" name="role_id">
                                            <option value="">Không có vai trò cụ thể</option>
                                            @foreach(\App\Models\Role::active()->orderByLevel()->get() as $role)
                                                <option value="{{ $role->id }}" 
                                                        {{ $user->role_id == $role->id ? 'selected' : '' }}
                                                        style="color: {{ $role->color }}">
                                                    {{ $role->display_name }} ({{ $role->permissions->count() }} quyền)
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Vai trò sẽ cấp một bộ quyền cơ bản</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mô tả vai trò</label>
                                        <div id="role-description" class="form-control bg-light border-0" style="min-height: 60px;">
                                            @if($user->roleModel)
                                                {{ $user->roleModel->description }}
                                            @else
                                                <em class="text-muted">Chọn vai trò để xem mô tả</em>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Permissions -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-cogs me-2"></i>
                                    Phân quyền chi tiết
                                    <small class="text-muted ms-2">(Ghi đè lên quyền từ vai trò)</small>
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($allPermissions as $group => $permissions)
                                    <div class="permission-group mb-4">
                                        <h6 class="text-primary border-bottom pb-2">
                                            <i class="fas fa-folder me-2"></i>
                                            {{ \App\Models\Permission::getGroups()[$group] ?? $group }}
                                        </h6>
                                        
                                        <div class="row">
                                            @foreach($permissions as $permission)
                                                @php
                                                    $hasFromRole = $user->roleModel && $user->roleModel->hasPermission($permission);
                                                    $currentState = $permissionStates[$permission->id] ?? 'default';
                                                    $isGranted = $currentState === 'grant';
                                                    $isDenied = $currentState === 'deny';
                                                    $isDefault = $currentState === 'default';
                                                @endphp
                                                
                                                <div class="col-md-6 mb-3">
                                                    <div class="card permission-card {{ $hasFromRole ? 'border-primary' : '' }}">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="card-title mb-1">{{ $permission->display_name }}</h6>
                                                                    <p class="card-text small text-muted mb-2">{{ $permission->description }}</p>
                                                                    
                                                                    @if($hasFromRole)
                                                                        <span class="badge bg-primary bg-opacity-25 text-primary">
                                                                            <i class="fas fa-user-tag me-1"></i>Từ vai trò
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="permission-controls">
                                                                    <div class="btn-group btn-group-sm" role="group">
                                                                        <input type="radio" 
                                                                               class="btn-check" 
                                                                               name="permission_{{ $permission->id }}" 
                                                                               id="default_{{ $permission->id }}" 
                                                                               value="default"
                                                                               {{ $isDefault ? 'checked' : '' }}>
                                                                        <label class="btn btn-outline-secondary" for="default_{{ $permission->id }}">
                                                                            <i class="fas fa-minus"></i>
                                                                        </label>

                                                                        <input type="radio" 
                                                                               class="btn-check" 
                                                                               name="permission_{{ $permission->id }}" 
                                                                               id="grant_{{ $permission->id }}" 
                                                                               value="grant"
                                                                               {{ $isGranted ? 'checked' : '' }}>
                                                                        <label class="btn btn-outline-success" for="grant_{{ $permission->id }}">
                                                                            <i class="fas fa-check"></i>
                                                                        </label>

                                                                        <input type="radio" 
                                                                               class="btn-check" 
                                                                               name="permission_{{ $permission->id }}" 
                                                                               id="deny_{{ $permission->id }}" 
                                                                               value="deny"
                                                                               {{ $isDenied ? 'checked' : '' }}>
                                                                        <label class="btn btn-outline-danger" for="deny_{{ $permission->id }}">
                                                                            <i class="fas fa-times"></i>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <strong>Mặc định:</strong> Theo vai trò | 
                                            <strong class="text-success">Cho phép:</strong> Cấp quyền | 
                                            <strong class="text-danger">Từ chối:</strong> Thu hồi quyền
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-times me-2"></i>Hủy
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Cập nhật phân quyền
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
}

.permission-card {
    transition: all 0.3s ease;
}

.permission-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.permission-controls .btn-group .btn {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.permission-group {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    background: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const roleDescription = document.getElementById('role-description');
    
    // Role descriptions
    const roleDescriptions = {
        @foreach(\App\Models\Role::active()->get() as $role)
            '{{ $role->id }}': '{{ $role->description }}',
        @endforeach
    };
    
    roleSelect.addEventListener('change', function() {
        const selectedRoleId = this.value;
        if (selectedRoleId && roleDescriptions[selectedRoleId]) {
            roleDescription.innerHTML = roleDescriptions[selectedRoleId];
        } else {
            roleDescription.innerHTML = '<em class="text-muted">Chọn vai trò để xem mô tả</em>';
        }
    });
    
    // Handle form submission by overriding submit button click
    const form = document.querySelector('form');
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default button click
            
            console.log('Submit button clicked - processing permissions...');
            
            // Remove any existing hidden permission inputs
            document.querySelectorAll('input[name="granted_permissions[]"], input[name="denied_permissions[]"]').forEach(input => {
                input.remove();
            });
            
            // Collect permissions from radio buttons
            const grantedPermissions = [];
            const deniedPermissions = [];
            
            document.querySelectorAll('input[name^="permission_"]:checked').forEach(input => {
                const permissionId = input.name.split('_')[1];
                const value = input.value;
                
                console.log(`Permission ${permissionId}: ${value}`);
                
                if (value === 'grant') {
                    grantedPermissions.push(permissionId);
                } else if (value === 'deny') {
                    deniedPermissions.push(permissionId);
                }
            });
            
            console.log('Granted permissions:', grantedPermissions);
            console.log('Denied permissions:', deniedPermissions);
            
            // Add hidden inputs for granted permissions
            grantedPermissions.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'granted_permissions[]';
                input.value = id;
                form.appendChild(input);
            });
            
            // Add hidden inputs for denied permissions  
            deniedPermissions.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'denied_permissions[]';
                input.value = id;
                form.appendChild(input);
            });
            
            console.log('Hidden inputs added, submitting form via submit()...');
            
            // Submit form programmatically
            form.submit();
        });
        
        console.log('Submit button click handler attached');
    } else {
        console.error('Submit button not found!');
    }
});
</script>
@endsection 