@extends('layouts.app')

@section('title', 'Chỉnh sửa người dùng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Chỉnh sửa người dùng
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Quản lý người dùng</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
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

            <!-- Form -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>
                                Cập nhật thông tin: {{ $user->name }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('users.update', $user) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <!-- Thông tin cơ bản -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Thông tin cơ bản
                                        </h6>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            Họ và tên <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required 
                                               autofocus>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">
                                            Tên đăng nhập <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('username') is-invalid @enderror" 
                                               id="username" 
                                               name="username" 
                                               value="{{ old('username', $user->username) }}" 
                                               placeholder="Chỉ chữ cái, số và dấu gạch dưới"
                                               required>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới (_)</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Thay đổi mật khẩu -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3">
                                            <i class="fas fa-lock me-2"></i>
                                            Thay đổi mật khẩu
                                            <small class="text-muted">(Để trống nếu không muốn thay đổi)</small>
                                        </h6>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            Mật khẩu mới
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            Xác nhận mật khẩu mới
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Phân quyền -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3">
                                            <i class="fas fa-shield-alt me-2"></i>
                                            Phân quyền và trạng thái
                                        </h6>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">
                                            Vai trò <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                            <option value="">Chọn vai trò</option>
                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Người dùng</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">
                                            Trạng thái <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="">Chọn trạng thái</option>
                                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Role Description -->
                                <div class="alert alert-info" id="roleDescription" style="display: none;">
                                    <div id="adminDescription" style="display: none;">
                                        <strong>Quản trị viên:</strong> Có toàn quyền truy cập, có thể tạo/sửa/xóa người dùng và quản lý phân quyền.
                                    </div>
                                    <div id="userDescription" style="display: none;">
                                        <strong>Người dùng:</strong> Chỉ có quyền truy cập các chức năng cơ bản của hệ thống.
                                    </div>
                                </div>

                                <!-- Cảnh báo thay đổi quan trọng -->
                                @if($user->isAdmin() && \App\Models\User::admins()->count() == 1)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Cảnh báo:</strong> Đây là admin duy nhất trong hệ thống. Hãy cẩn thận khi thay đổi vai trò hoặc trạng thái.
                                    </div>
                                @endif

                                @if($user->id === auth()->id())
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Lưu ý:</strong> Bạn đang chỉnh sửa tài khoản của chính mình.
                                    </div>
                                @endif

                                <!-- Thông tin bổ sung -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-3">
                                            <i class="fas fa-clock me-2"></i>
                                            Thông tin bổ sung
                                        </h6>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ngày tạo</label>
                                        <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i') }}" readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Đăng nhập cuối</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa đăng nhập' }}" 
                                               readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Người tạo</label>
                                        <input type="text" class="form-control" 
                                               value="{{ $user->creator ? $user->creator->name : 'Hệ thống' }}" 
                                               readonly>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Số người dùng đã tạo</label>
                                        <input type="text" class="form-control" value="{{ $user->createdUsers->count() }}" readonly>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <hr class="my-4">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Cập nhật thông tin
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    function setupPasswordToggle(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        
        if (input && button) {
            button.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });
        }
    }
    
    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('password_confirmation', 'togglePasswordConfirm');
    
    // Role description
    const roleSelect = document.getElementById('role');
    const roleDescription = document.getElementById('roleDescription');
    const adminDescription = document.getElementById('adminDescription');
    const userDescription = document.getElementById('userDescription');
    
    function updateRoleDescription() {
        const value = roleSelect.value;
        
        if (value === 'admin') {
            roleDescription.style.display = 'block';
            adminDescription.style.display = 'block';
            userDescription.style.display = 'none';
        } else if (value === 'user') {
            roleDescription.style.display = 'block';
            adminDescription.style.display = 'none';
            userDescription.style.display = 'block';
        } else {
            roleDescription.style.display = 'none';
        }
    }
    
    roleSelect.addEventListener('change', updateRoleDescription);
    // Show initial description
    updateRoleDescription();
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        
        // Only validate if password fields are filled
        if (password || passwordConfirm) {
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
        }
    });
});
</script>
@endsection 