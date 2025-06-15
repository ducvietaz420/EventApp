@extends('layouts.app')

@section('title', 'Quản lý Timeline')
@section('page-title', 'Quản lý Timeline')

@section('page-actions')
    <a href="{{ route('timelines.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Thêm mốc thời gian
    </a>
@endsection

@section('content')
<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        <p class="mb-0">Tổng mốc thời gian</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['completed'] ?? 0 }}</h4>
                        <p class="mb-0">Đã hoàn thành</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                        <p class="mb-0">Đang chờ</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['overdue'] ?? 0 }}</h4>
                        <p class="mb-0">Quá hạn</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc và tìm kiếm -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('timelines.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Tìm theo tiêu đề, mô tả...">
                </div>
                <div class="col-md-2">
                    <label for="event_id" class="form-label">Sự kiện</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tất cả sự kiện</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Đang chờ</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Quá hạn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="priority" class="form-label">Độ ưu tiên</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">Tất cả</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Thấp</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Cao</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_range" class="form-label">Thời gian</label>
                    <select class="form-select" id="date_range" name="date_range">
                        <option value="">Tất cả</option>
                        <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>Tháng này</option>
                        <option value="upcoming" {{ request('date_range') === 'upcoming' ? 'selected' : '' }}>Sắp tới</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách timeline -->
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách mốc thời gian</h5>
        <div class="d-flex gap-2">
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="view" id="listView" autocomplete="off" checked>
                <label class="btn btn-outline-primary" for="listView">
                    <i class="fas fa-list"></i> Danh sách
                </label>
                
                <input type="radio" class="btn-check" name="view" id="timelineView" autocomplete="off">
                <label class="btn btn-outline-primary" for="timelineView">
                    <i class="fas fa-project-diagram"></i> Timeline
                </label>
            </div>
            <button type="button" class="btn btn-outline-success" onclick="exportTimelines()">
                <i class="fas fa-download me-2"></i>Xuất Excel
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- List View -->
        <div id="listViewContent">
            @if($timelines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Tiêu đề</th>
                                <th>Sự kiện</th>
                                <th>Ngày</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th>Độ ưu tiên</th>
                                <th width="15%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timelines as $timeline)
                                <tr class="{{ $timeline->due_date && $timeline->due_date->isPast() && !$timeline->is_completed ? 'table-danger' : '' }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input timeline-checkbox" value="{{ $timeline->id }}">
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $timeline->title }}</div>
                                        @if($timeline->description)
                                            <small class="text-muted">{{ Str::limit($timeline->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('events.show', $timeline->event) }}" class="text-decoration-none">
                                            {{ $timeline->event->name }}
                                        </a>
                                        <br><small class="text-muted">{{ ucfirst($timeline->event->type) }}</small>
                                    </td>
                                    <td>
                                        @if($timeline->start_time)
                                            {{ $timeline->start_time->format('d/m/Y') }}
                                            @if($timeline->start_time->isToday())
                                                <span class="badge bg-warning ms-1">Hôm nay</span>
                                            @elseif($timeline->start_time->isTomorrow())
                                                <span class="badge bg-info ms-1">Ngày mai</span>
                                            @elseif($timeline->start_time->isPast() && $timeline->status !== 'completed')
                                                <span class="badge bg-danger ms-1">Quá hạn</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Chưa xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($timeline->start_time)
                                            {{ $timeline->start_time->format('H:i') }}
                                        @else
                                            <span class="text-muted">Cả ngày</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($timeline->status === 'completed')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @elseif($timeline->end_time && $timeline->end_time->isPast() && $timeline->status !== 'completed')
                                            <span class="badge bg-danger">Quá hạn</span>
                                        @elseif($timeline->status === 'in_progress')
                                            <span class="badge bg-primary">Đang tiến hành</span>
                                        @else
                                            <span class="badge bg-warning">Đang chờ</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($timeline->priority)
                                            @case('high')
                                                <span class="badge bg-danger">Cao</span>
                                                @break
                                            @case('medium')
                                                <span class="badge bg-warning">Trung bình</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Thấp</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('timelines.show', $timeline) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('timelines.edit', $timeline) }}" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($timeline->status !== 'completed')
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="markCompleted({{ $timeline->id }})" title="Đánh dấu hoàn thành">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteTimeline({{ $timeline->id }})" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Phân trang -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Hiển thị {{ $timelines->firstItem() ?? 0 }} đến {{ $timelines->lastItem() ?? 0 }} 
                        trong tổng số {{ $timelines->total() }} mốc thời gian
                    </div>
                    <div>
                        {{ $timelines->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có mốc thời gian nào</h5>
                    <p class="text-muted">Hãy tạo mốc thời gian đầu tiên cho sự kiện của bạn.</p>
                    <a href="{{ route('timelines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm mốc thời gian
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Timeline View -->
        <div id="timelineViewContent" style="display: none;">
            @if($timelines->count() > 0)
                <div class="timeline-container">
                    @foreach($timelines->groupBy(function($timeline) { return $timeline->start_time ? $timeline->start_time->format('Y-m-d') : 'no-date'; }) as $date => $dayTimelines)
                        <div class="timeline-day mb-4">
                            <h6 class="timeline-date">
                                @if($date === 'no-date')
                                    Chưa xác định ngày
                                @else
                                    {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                    @if(\Carbon\Carbon::parse($date)->isToday())
                                        <span class="badge bg-warning ms-2">Hôm nay</span>
                                    @elseif(\Carbon\Carbon::parse($date)->isTomorrow())
                                        <span class="badge bg-info ms-2">Ngày mai</span>
                                    @elseif(\Carbon\Carbon::parse($date)->isPast())
                                        <span class="badge bg-danger ms-2">Đã qua</span>
                                    @endif
                                @endif
                            </h6>
                            <div class="timeline-items">
                                @foreach($dayTimelines->sortBy('start_time') as $timeline)
                                                                            <div class="timeline-item {{ $timeline->status === 'completed' ? 'completed' : '' }}">
                                        <div class="timeline-marker {{ $timeline->status === 'completed' ? 'bg-success' : ($timeline->end_time && $timeline->end_time->isPast() ? 'bg-danger' : 'bg-primary') }}"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $timeline->title }}</h6>
                                                    <p class="mb-1 text-muted">{{ $timeline->description }}</p>
                                                    <div class="d-flex gap-2 mb-2">
                                                        <span class="badge bg-secondary">{{ $timeline->event->name }}</span>
                                                        @switch($timeline->priority)
                                                            @case('high')
                                                                <span class="badge bg-danger">Cao</span>
                                                                @break
                                                            @case('medium')
                                                                <span class="badge bg-warning">Trung bình</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">Thấp</span>
                                                        @endswitch
                                                    </div>
                                                    @if($timeline->start_time)
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>{{ $timeline->start_time->format('H:i') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('timelines.show', $timeline) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('timelines.edit', $timeline) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($timeline->status !== 'completed')
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="markCompleted({{ $timeline->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có timeline nào để hiển thị</h5>
                    <p class="text-muted">Hãy tạo mốc thời gian đầu tiên cho sự kiện của bạn.</p>
                    <a href="{{ route('timelines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm mốc thời gian
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card shadow mt-4" id="bulkActions" style="display: none;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span id="selectedCount">0</span> mốc thời gian được chọn
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success" onclick="bulkMarkCompleted()">
                    <i class="fas fa-check me-2"></i>Đánh dấu hoàn thành
                </button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-flag me-2"></i>Thay đổi độ ưu tiên
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkChangePriority('low')">
                            <span class="badge bg-secondary me-2">Thấp</span>Độ ưu tiên thấp
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkChangePriority('medium')">
                            <span class="badge bg-warning me-2">Trung bình</span>Độ ưu tiên trung bình
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkChangePriority('high')">
                            <span class="badge bg-danger me-2">Cao</span>Độ ưu tiên cao
                        </a></li>
                    </ul>
                </div>
                <button type="button" class="btn btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash me-2"></i>Xóa
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-container {
    position: relative;
}

.timeline-day {
    border-left: 3px solid #dee2e6;
    padding-left: 20px;
    margin-left: 10px;
}

.timeline-date {
    background: #fff;
    padding: 5px 10px;
    margin-left: -30px;
    border: 2px solid #dee2e6;
    border-radius: 20px;
    display: inline-block;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 30px;
}

.timeline-item.completed {
    opacity: 0.7;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 10px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}

.timeline-item.completed .timeline-content {
    background: #e8f5e8;
    border-left-color: #28a745;
}

.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

/* Toast notifications */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    min-width: 300px;
    transform: translateX(400px);
    transition: transform 0.3s ease-in-out;
}

.toast-notification.show {
    transform: translateX(0);
}

.toast-content {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: white;
    border-left: 4px solid;
}

.toast-success .toast-content {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%);
}

.toast-error .toast-content {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #ffffff 100%);
}

.toast-warning .toast-content {
    border-left-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd 0%, #ffffff 100%);
}

.toast-info .toast-content {
    border-left-color: #17a2b8;
    background: linear-gradient(135deg, #d1ecf1 0%, #ffffff 100%);
}

.toast-icon {
    margin-right: 12px;
    font-size: 20px;
}

.toast-success .toast-icon {
    color: #28a745;
}

.toast-error .toast-icon {
    color: #dc3545;
}

.toast-warning .toast-icon {
    color: #ffc107;
}

.toast-info .toast-icon {
    color: #17a2b8;
}

.toast-message {
    flex: 1;
    font-weight: 500;
    color: #333;
}

.toast-close {
    background: none;
    border: none;
    font-size: 20px;
    font-weight: bold;
    color: #666;
    cursor: pointer;
    padding: 0;
    margin-left: 15px;
    line-height: 1;
}

.toast-close:hover {
    color: #000;
}
</style>
@endpush

@push('scripts')
<script>
// Toggle view
document.getElementById('listView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('listViewContent').style.display = 'block';
        document.getElementById('timelineViewContent').style.display = 'none';
    }
});

document.getElementById('timelineView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('listViewContent').style.display = 'none';
        document.getElementById('timelineViewContent').style.display = 'block';
    }
});

// Auto submit form when filters change
document.querySelectorAll('#filterForm select').forEach(select => {
    select.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});

// Clear filters
function clearFilters() {
    const form = document.getElementById('filterForm');
    form.querySelectorAll('input, select').forEach(input => {
        if (input.type === 'text' || input.type === 'search') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    });
    form.submit();
}

// Select all checkboxes
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.timeline-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

// Individual checkbox change
document.querySelectorAll('.timeline-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.timeline-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Mark timeline as completed
function markCompleted(timelineId) {
    if (confirm('Bạn có chắc chắn muốn đánh dấu mốc thời gian này là hoàn thành?')) {
        fetch(`/timelines/${timelineId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Có lỗi xảy ra khi cập nhật trạng thái!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi cập nhật trạng thái!', 'error');
        });
    }
}

// Delete timeline
function deleteTimeline(timelineId) {
    if (confirm('Bạn có chắc chắn muốn xóa mốc thời gian này?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/timelines/${timelineId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk actions
function bulkMarkCompleted() {
    const checkedBoxes = document.querySelectorAll('.timeline-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        showToast('Vui lòng chọn ít nhất một mốc thời gian!', 'warning');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn đánh dấu ${ids.length} mốc thời gian là hoàn thành?`)) {
        fetch('/timelines/bulk-complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Có lỗi xảy ra khi cập nhật!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi cập nhật!', 'error');
        });
    }
}

function bulkChangePriority(priority) {
    const checkedBoxes = document.querySelectorAll('.timeline-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        showToast('Vui lòng chọn ít nhất một mốc thời gian!', 'warning');
        return;
    }
    
    const priorityNames = {
        'low': 'Thấp',
        'medium': 'Trung bình',
        'high': 'Cao'
    };
    
    if (confirm(`Bạn có chắc chắn muốn thay đổi độ ưu tiên thành "${priorityNames[priority]}" cho ${ids.length} mốc thời gian?`)) {
        fetch('/timelines/bulk-priority', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids: ids, priority: priority })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Có lỗi xảy ra khi cập nhật!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi cập nhật!', 'error');
        });
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.timeline-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        showToast('Vui lòng chọn ít nhất một mốc thời gian!', 'warning');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn xóa ${ids.length} mốc thời gian? Hành động này không thể hoàn tác!`)) {
        fetch('/timelines/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Có lỗi xảy ra khi xóa!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi xóa!', 'error');
        });
    }
}

// Export timelines
function exportTimelines() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `{{ route('timelines.index') }}?${params.toString()}`;
}

// Toast notification function
function showToast(message, type = 'info') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-icon">
                ${getToastIcon(type)}
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;

    // Add to page
    document.body.appendChild(toast);

    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

function getToastIcon(type) {
    switch(type) {
        case 'success': return '<i class="fas fa-check-circle"></i>';
        case 'error': return '<i class="fas fa-exclamation-circle"></i>';
        case 'warning': return '<i class="fas fa-exclamation-triangle"></i>';
        default: return '<i class="fas fa-info-circle"></i>';
    }
}
</script>
@endpush