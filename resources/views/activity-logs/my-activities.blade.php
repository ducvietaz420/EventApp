@extends('layouts.app')

@section('title', 'Hoạt động của tôi')
@section('page-title', 'Hoạt động của tôi')

@section('page-actions')
    @if(auth()->user()->hasPermission('activity_logs.view_all'))
        <a href="{{ route('activity-logs.index') }}" class="btn btn-primary">
            <i class="fas fa-list me-1"></i>Tất cả hoạt động
        </a>
    @endif
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
                <form method="GET" action="{{ route('activity-logs.my-activities') }}">
                    <div class="row g-3">
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
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Từ ngày</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Đến ngày</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                   value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
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
                            <a href="{{ route('activity-logs.my-activities') }}" class="btn btn-secondary">
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
                    <i class="fas fa-user-clock me-2"></i>Hoạt động của tôi 
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
                                    <th>Hành động</th>
                                    <th>Mô tả</th>
                                    <th>IP Address</th>
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
                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Không có hoạt động nào</h5>
                        <p class="text-muted">Bạn chưa có hoạt động nào được ghi lại với các bộ lọc hiện tại.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mt-4" id="activitySummary">
    <div class="col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Tổng hoạt động</h5>
                        <h3 class="mb-0">{{ $activityLogs->total() }}</h3>
                        <small>Tất cả hoạt động đã ghi lại</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-calendar-day fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Hôm nay</h5>
                        <h3 class="mb-0">
                            {{ $activityLogs->where('created_at', '>=', now()->startOfDay())->count() }}
                        </h3>
                        <small>Hoạt động trong ngày</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-hover {
        transition: transform 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
    }
</style>
@endpush 