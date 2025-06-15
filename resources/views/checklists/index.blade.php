@extends('layouts.app')

@section('title', 'Danh sách công việc')
@section('page-title', 'Quản lý danh sách công việc')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('checklists.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm công việc
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-2"></i>Xuất dữ liệu
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportToExcel()">
                    <i class="fas fa-file-excel text-success me-2"></i>Xuất Excel
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf text-danger me-2"></i>Xuất PDF
                </a></li>
            </ul>
        </div>
    </div>
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
                        <p class="mb-0">Tổng công việc</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-2x"></i>
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
                        <p class="mb-0">Hoàn thành</p>
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
                        <i class="fas fa-clock fa-2x"></i>
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
    <div class="card-header">
        <h6 class="mb-0">Bộ lọc và tìm kiếm</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('checklists.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Tìm theo tên, mô tả...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Sự kiện</label>
                        <select class="form-select" id="event_id" name="event_id" onchange="autoSubmit()">
                            <option value="">Tất cả sự kiện</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status" onchange="autoSubmit()">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Đang chờ</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Độ ưu tiên</label>
                        <select class="form-select" id="priority" name="priority" onchange="autoSubmit()">
                            <option value="">Tất cả độ ưu tiên</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Thấp</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Cao</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Người phụ trách</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" onchange="autoSubmit()">
                            <option value="">Tất cả</option>
                            @foreach($assignees as $assignee)
                                <option value="{{ $assignee }}" {{ request('assigned_to') === $assignee ? 'selected' : '' }}>
                                    {{ $assignee }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()" title="Xóa bộ lọc">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="due_date_from" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="due_date_from" name="due_date_from" 
                               value="{{ request('due_date_from') }}" onchange="autoSubmit()">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="due_date_to" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="due_date_to" name="due_date_to" 
                               value="{{ request('due_date_to') }}" onchange="autoSubmit()">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="sort_by" class="form-label">Sắp xếp theo</label>
                        <select class="form-select" id="sort_by" name="sort_by" onchange="autoSubmit()">
                            <option value="due_date" {{ request('sort_by', 'due_date') === 'due_date' ? 'selected' : '' }}>Ngày hạn</option>
                            <option value="priority" {{ request('sort_by') === 'priority' ? 'selected' : '' }}>Độ ưu tiên</option>
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                            <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Tên công việc</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="sort_direction" class="form-label">Thứ tự</label>
                        <select class="form-select" id="sort_direction" name="sort_direction" onchange="autoSubmit()">
                            <option value="asc" {{ request('sort_direction', 'asc') === 'asc' ? 'selected' : '' }}>Tăng dần</option>
                            <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>Giảm dần</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Hành động hàng loạt -->
<div class="card shadow mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span id="selectedCount">0</span> công việc được chọn
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success" onclick="bulkMarkCompleted()">
                    <i class="fas fa-check me-2"></i>Đánh dấu hoàn thành
                </button>
                <button type="button" class="btn btn-warning" onclick="bulkChangePriority()">
                    <i class="fas fa-flag me-2"></i>Thay đổi độ ưu tiên
                </button>
                <button type="button" class="btn btn-info" onclick="bulkAssign()">
                    <i class="fas fa-user me-2"></i>Gán người phụ trách
                </button>
                <button type="button" class="btn btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash me-2"></i>Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách công việc -->
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Danh sách công việc ({{ $checklists->total() }} kết quả)</h6>
        <div class="btn-group btn-group-sm" role="group">
            <input type="radio" class="btn-check" name="viewMode" id="listView" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="listView">
                <i class="fas fa-list"></i> Danh sách
            </label>
            
            <input type="radio" class="btn-check" name="viewMode" id="cardView" autocomplete="off">
            <label class="btn btn-outline-primary" for="cardView">
                <i class="fas fa-th-large"></i> Thẻ
            </label>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- List View -->
        <div id="listViewContent">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </div>
                            </th>
                            <th>Công việc</th>
                            <th>Sự kiện</th>
                            <th>Người phụ trách</th>
                            <th>Ngày hạn</th>
                            <th>Độ ưu tiên</th>
                            <th>Trạng thái</th>
                            <th>Tiến độ</th>
                            <th width="120">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checklists as $checklist)
                            <tr class="{{ $checklist->is_completed ? 'table-success' : ($checklist->due_date && $checklist->due_date->isPast() ? 'table-warning' : '') }}">
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input item-checkbox" type="checkbox" 
                                               value="{{ $checklist->id }}" onchange="updateBulkActions()">
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ route('checklists.show', $checklist) }}" class="text-decoration-none fw-bold">
                                            {{ $checklist->title }}
                                        </a>
                                        @if($checklist->description)
                                            <br><small class="text-muted">{{ Str::limit($checklist->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('events.show', $checklist->event) }}" class="text-decoration-none">
                                        {{ $checklist->event->name }}
                                    </a>
                                    <br><small class="text-muted">{{ ucfirst($checklist->event->type) }}</small>
                                </td>
                                <td>
                                    @if($checklist->assigned_to)
                                        <div>
                                            <i class="fas fa-user text-muted me-1"></i>{{ $checklist->assigned_to }}
                                        </div>
                                    @else
                                        <span class="text-muted">Chưa gán</span>
                                    @endif
                                </td>
                                <td>
                                    @if($checklist->due_date)
                                        <div>
                                            {{ $checklist->due_date->format('d/m/Y') }}
                                            @if($checklist->due_time)
                                                <br><small class="text-muted">{{ $checklist->due_time->format('H:i') }}</small>
                                            @endif
                                        </div>
                                        @if($checklist->due_date->isToday())
                                            <span class="badge bg-warning">Hôm nay</span>
                                        @elseif($checklist->due_date->isTomorrow())
                                            <span class="badge bg-info">Ngày mai</span>
                                        @elseif($checklist->due_date->isPast() && !$checklist->is_completed)
                                            <span class="badge bg-danger">Quá hạn</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Không xác định</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary') }}">
                                        @switch($checklist->priority)
                                            @case('high')
                                                <i class="fas fa-flag me-1"></i>Cao
                                                @break
                                            @case('medium')
                                                <i class="fas fa-flag me-1"></i>Trung bình
                                                @break
                                            @default
                                                <i class="fas fa-flag me-1"></i>Thấp
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    @if($checklist->is_completed)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Hoàn thành
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Đang chờ
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $progress = $checklist->is_completed ? 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $progress === 100 ? 'success' : 'primary' }}" 
                                             style="width: {{ $progress }}%">{{ $progress }}%</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-outline-info" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-outline-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$checklist->is_completed)
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="markCompleted({{ $checklist->id }})" title="Hoàn thành">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteChecklist({{ $checklist->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-tasks fa-3x mb-3"></i>
                                        <h5>Không có công việc nào</h5>
                                        <p>Hãy thêm công việc đầu tiên cho sự kiện của bạn.</p>
                                        <a href="{{ route('checklists.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Thêm công việc
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Card View -->
        <div id="cardViewContent" style="display: none;">
            <div class="p-3">
                <div class="row">
                    @forelse($checklists as $checklist)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 {{ $checklist->is_completed ? 'border-success' : ($checklist->due_date && $checklist->due_date->isPast() ? 'border-warning' : '') }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input item-checkbox" type="checkbox" 
                                               value="{{ $checklist->id }}" onchange="updateBulkActions()">
                                    </div>
                                    <span class="badge bg-{{ $checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($checklist->priority) }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('checklists.show', $checklist) }}" class="text-decoration-none">
                                            {{ $checklist->title }}
                                        </a>
                                    </h6>
                                    @if($checklist->description)
                                        <p class="card-text text-muted small">{{ Str::limit($checklist->description, 80) }}</p>
                                    @endif
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Sự kiện:</small><br>
                                        <a href="{{ route('events.show', $checklist->event) }}" class="text-decoration-none">
                                            {{ $checklist->event->name }}
                                        </a>
                                    </div>
                                    
                                    @if($checklist->assigned_to)
                                        <div class="mb-2">
                                            <small class="text-muted">Người phụ trách:</small><br>
                                            <i class="fas fa-user text-muted me-1"></i>{{ $checklist->assigned_to }}
                                        </div>
                                    @endif
                                    
                                    @if($checklist->due_date)
                                        <div class="mb-2">
                                            <small class="text-muted">Hạn:</small><br>
                                            {{ $checklist->due_date->format('d/m/Y') }}
                                            @if($checklist->due_time)
                                                {{ $checklist->due_time->format('H:i') }}
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="progress mb-2" style="height: 8px;">
                                        @php $progress = $checklist->is_completed ? 100 : 0; @endphp
                                        <div class="progress-bar bg-{{ $progress === 100 ? 'success' : 'primary' }}" 
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($checklist->is_completed)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Hoàn thành
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Đang chờ
                                                </span>
                                            @endif
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-outline-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$checklist->is_completed)
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="markCompleted({{ $checklist->id }})" title="Hoàn thành">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <h5>Không có công việc nào</h5>
                                <p class="text-muted">Hãy thêm công việc đầu tiên cho sự kiện của bạn.</p>
                                <a href="{{ route('checklists.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Thêm công việc
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    @if($checklists->hasPages())
        <div class="card-footer">
            {{ $checklists->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto submit form when filters change
function autoSubmit() {
    document.getElementById('filterForm').submit();
}

// Clear all filters
function clearFilters() {
    const form = document.getElementById('filterForm');
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
    form.submit();
}

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkCard = document.getElementById('bulkActionsCard');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkCard.style.display = 'block';
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkCard.style.display = 'none';
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
}

// Switch between list and card view
document.getElementById('listView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('listViewContent').style.display = 'block';
        document.getElementById('cardViewContent').style.display = 'none';
    }
});

document.getElementById('cardView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('listViewContent').style.display = 'none';
        document.getElementById('cardViewContent').style.display = 'block';
    }
});

// Mark checklist as completed
function markCompleted(checklistId) {
    if (confirm('Bạn có chắc chắn muốn đánh dấu công việc này là hoàn thành?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}/complete`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete checklist
function deleteChecklist(checklistId) {
    if (confirm('Bạn có chắc chắn muốn xóa công việc này? Hành động này không thể hoàn tác!')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk actions
function bulkMarkCompleted() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn đánh dấu ${selected.length} công việc là hoàn thành?`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-complete';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkChangePriority() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    const priority = prompt('Chọn độ ưu tiên mới (low/medium/high):', 'medium');
    if (priority && ['low', 'medium', 'high'].includes(priority)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-priority';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const priorityInput = document.createElement('input');
        priorityInput.type = 'hidden';
        priorityInput.name = 'priority';
        priorityInput.value = priority;
        form.appendChild(priorityInput);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkAssign() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    const assignee = prompt('Nhập tên người phụ trách:');
    if (assignee) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-assign';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const assigneeInput = document.createElement('input');
        assigneeInput.type = 'hidden';
        assigneeInput.name = 'assigned_to';
        assigneeInput.value = assignee;
        form.appendChild(assigneeInput);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkDelete() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn xóa ${selected.length} công việc? Hành động này không thể hoàn tác!`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-delete';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Export functions
function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = '{{ route("checklists.index") }}?' + params.toString();
}

function exportToPDF() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'pdf');
    window.location.href = '{{ route("checklists.index") }}?' + params.toString();
}
</script>
@endpush