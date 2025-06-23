@extends('layouts.app')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-users text-primary me-2"></i>
                        Quản lý người dùng
                    </h2>
                    <p class="text-muted mb-0">Quản lý tài khoản và phân quyền người dùng</p>
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm người dùng
                </a>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Tên hoặc username...">
                        </div>
                        <div class="col-md-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Người dùng</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-1"></i>Lọc
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Người dùng</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Người tạo</th>
                                        <th>Đăng nhập cuối</th>
                                        <th>Ngày tạo</th>
                                        <th width="180">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->name }}</div>
                                                        <div class="text-primary fw-medium">
                                                            @if($user->username)
                                                                &#64;{{ $user->username }}
                                                            @else
                                                                <span class="text-muted">Chưa có username</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                                    <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user' }} me-1"></i>
                                                    {{ $user->role_display }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    <i class="fas {{ $user->status === 'active' ? 'fa-check' : 'fa-ban' }} me-1"></i>
                                                    {{ $user->status_display }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->creator)
                                                    <small>{{ $user->creator->name }}</small>
                                                @else
                                                    <small class="text-muted">Hệ thống</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->last_login_at)
                                                    <small>{{ $user->last_login_at->format('d/m/Y H:i') }}</small>
                                                @else
                                                    <small class="text-muted">Chưa đăng nhập</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('users.permissions', $user) }}" class="btn btn-sm btn-outline-warning" title="Phân quyền">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </a>
                                                    
                                                    @if($user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="btn btn-sm {{ $user->isActive() ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                                    title="{{ $user->isActive() ? 'Tạm khóa' : 'Kích hoạt' }}"
                                                                    onclick="return confirm('Bạn có chắc muốn {{ $user->isActive() ? 'tạm khóa' : 'kích hoạt' }} tài khoản này?')">
                                                                <i class="fas {{ $user->isActive() ? 'fa-ban' : 'fa-check' }}"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Xóa"
                                                                    onclick="return confirm('Bạn có chắc muốn xóa tài khoản {{ $user->name }}? Hành động này không thể hoàn tác!')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Hiển thị {{ $users->firstItem() }}-{{ $users->lastItem() }} trong {{ $users->total() }} người dùng
                            </div>
                            {{ $users->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không tìm thấy người dùng nào</h5>
                            <p class="text-muted">Thử thay đổi bộ lọc hoặc tạo người dùng mới</p>
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm người dùng đầu tiên
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}
</style>
@endsection 