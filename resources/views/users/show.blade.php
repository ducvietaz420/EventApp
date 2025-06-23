@extends('layouts.app')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-user text-primary me-2"></i>
                        Chi tiết người dùng
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Quản lý người dùng</a></li>
                            <li class="breadcrumb-item active">{{ $user->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="btn-group">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Thông tin cơ bản -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Thông tin cơ bản
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="avatar-circle-large mx-auto mb-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-3">{{ $user->email }}</p>
                            
                            <div class="row text-center">
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
                        </div>
                    </div>

                    <!-- Thông tin tài khoản -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Thông tin tài khoản
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Đăng nhập cuối:</div>
                                <div class="col-7">
                                    @if($user->last_login_at)
                                        <strong>{{ $user->last_login_at->format('d/m/Y H:i') }}</strong>
                                        <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Chưa đăng nhập</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Ngày tạo:</div>
                                <div class="col-7">
                                    <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                                    <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Cập nhật cuối:</div>
                                <div class="col-7">
                                    <strong>{{ $user->updated_at->format('d/m/Y H:i') }}</strong>
                                    <br><small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5 text-muted">Người tạo:</div>
                                <div class="col-7">
                                    @if($user->creator)
                                        <strong>{{ $user->creator->name }}</strong>
                                        <br><small class="text-muted">{{ $user->creator->email }}</small>
                                    @else
                                        <span class="text-muted">Hệ thống</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Thống kê hoạt động
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Số người dùng đã tạo:</span>
                                        <strong class="text-primary">{{ $user->createdUsers->count() }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chi tiết và hoạt động -->
                <div class="col-lg-8">
                    <!-- Quick Actions -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-tools me-2"></i>
                                Thao tác nhanh
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('users.permissions', $user) }}" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-shield-alt me-2"></i>Quản lý phân quyền
                                    </a>
                                </div>
                                
                                @if($user->id !== auth()->id())
                                    <div class="col-md-6 mb-3">
                                        <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="w-100">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn {{ $user->isActive() ? 'btn-outline-warning' : 'btn-outline-success' }} w-100"
                                                    onclick="return confirm('Bạn có chắc muốn {{ $user->isActive() ? 'tạm khóa' : 'kích hoạt' }} tài khoản này?')">
                                                <i class="fas {{ $user->isActive() ? 'fa-ban' : 'fa-check' }} me-2"></i>
                                                {{ $user->isActive() ? 'Tạm khóa tài khoản' : 'Kích hoạt tài khoản' }}
                                            </button>
                                        </form>
                                    </div>

                                    @if(!$user->isAdmin() || \App\Models\User::admins()->count() > 1)
                                        <div class="col-md-12">
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="w-100">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger w-100"
                                                        onclick="return confirm('Bạn có chắc muốn xóa tài khoản {{ $user->name }}? Hành động này không thể hoàn tác!')">
                                                    <i class="fas fa-trash me-2"></i>Xóa tài khoản
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Người dùng đã tạo -->
                    @if($user->createdUsers->count() > 0)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-users me-2"></i>
                                    Người dùng đã tạo ({{ $user->createdUsers->count() }})
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tên</th>
                                                <th>Email</th>
                                                <th>Vai trò</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày tạo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->createdUsers as $createdUser)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('users.show', $createdUser) }}" class="text-decoration-none">
                                                            {{ $createdUser->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $createdUser->email }}</td>
                                                    <td>
                                                        <span class="badge {{ $createdUser->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                                                            {{ $createdUser->role_display }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $createdUser->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $createdUser->status_display }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $createdUser->created_at->format('d/m/Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Lịch sử hoạt động (placeholder) -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Lịch sử hoạt động
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Tài khoản được tạo</h6>
                                        <p class="mb-0 text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                
                                @if($user->last_login_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Đăng nhập cuối cùng</h6>
                                            <p class="mb-0 text-muted">{{ $user->last_login_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($user->updated_at != $user->created_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-warning">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Thông tin được cập nhật</h6>
                                            <p class="mb-0 text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
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

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    border: 3px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>
@endsection 