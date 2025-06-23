@extends('layouts.app')

@section('title', 'Lịch sử hoạt động')
@section('page-title', 'Lịch sử hoạt động')

@section('page-actions')
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cleanupModal">
            <i class="fas fa-trash-alt me-1"></i>Dọn dẹp
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>Bộ lọc
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('activity-logs.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">Người dùng</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Tất cả người dùng</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="action" class="form-label">Hành động</label>
                            <select name="action" id="action" class="form-select">
                                <option value="">Tất cả hành động</option>
                                @foreach($actions as $value => $label)
                                    <option value="{{ $value }}" {{ request('action') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">Từ ngày</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">Đến ngày</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                   value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Từ khóa..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Lọc
                            </button>
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Xóa bộ lọc
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Lịch sử hoạt động 
                    <span class="badge bg-primary">{{ $activityLogs->total() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @if($activityLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Người dùng</th>
                                    <th>Hành động</th>
                                    <th>Mô tả</th>
                                    <th>IP</th>
                                    <th width="80">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activityLogs as $log)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ $log->created_at->format('d/m/Y H:i:s') }}<br>
                                                <em>{{ $log->created_at->diffForHumans() }}</em>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2" style="background: #6c757d;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $log->user->name ?? 'Người dùng đã xóa' }}</div>
                                                    <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->action_class }}">
                                                <i class="{{ $log->action_icon }} me-1"></i>
                                                {{ $log->action_display }}
                                            </span>
                                        </td>
                                        <td>{{ $log->description }}</td>
                                        <td>
                                            <small class="text-muted">{{ $log->ip_address }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('activity-logs.show', $log) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer">
                        {{ $activityLogs->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có lịch sử hoạt động</h5>
                        <p class="text-muted">Chưa có hoạt động nào được ghi lại với các bộ lọc hiện tại.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('activity-logs.cleanup') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dọn dẹp lịch sử hoạt động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="days" class="form-label">Xóa các bản ghi cũ hơn (ngày)</label>
                        <input type="number" name="days" id="days" class="form-control" 
                               min="1" max="365" value="90" required>
                        <div class="form-text">Nhập số ngày để xóa các bản ghi cũ hơn thời gian đó.</div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Cảnh báo:</strong> Hành động này không thể hoàn tác!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-trash-alt me-1"></i>Dọn dẹp
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }
</style>
@endpush 