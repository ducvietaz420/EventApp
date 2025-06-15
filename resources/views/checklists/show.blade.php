@extends('layouts.app')

@section('title', 'Chi tiết công việc')
@section('page-title', 'Chi tiết công việc: ' . $checklist->title)

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('checklists.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
        <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-2"></i>Hành động
            </button>
            <ul class="dropdown-menu">
                <li>
                    @if(!$checklist->is_completed)
                        <a class="dropdown-item" href="#" onclick="toggleComplete({{ $checklist->id }}, true)">
                            <i class="fas fa-check-circle text-success me-2"></i>Đánh dấu hoàn thành
                        </a>
                    @else
                        <a class="dropdown-item" href="#" onclick="toggleComplete({{ $checklist->id }}, false)">
                            <i class="fas fa-undo text-warning me-2"></i>Đánh dấu chưa hoàn thành
                        </a>
                    @endif
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="#" onclick="duplicateChecklist({{ $checklist->id }})">
                        <i class="fas fa-copy text-info me-2"></i>Nhân bản công việc
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('events.show', $checklist->event) }}">
                        <i class="fas fa-calendar text-primary me-2"></i>Xem sự kiện
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="exportChecklist()">
                        <i class="fas fa-download text-secondary me-2"></i>Xuất báo cáo
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Xóa công việc
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Thông tin chính -->
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Thông tin công việc</h6>
                <div>
                    @if($checklist->is_important)
                        <span class="badge bg-warning me-2">
                            <i class="fas fa-star"></i> Quan trọng
                        </span>
                    @endif
                    <span class="badge bg-{{ $checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($checklist->priority) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">{{ $checklist->title }}</h5>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sự kiện:</label>
                            <div>
                                <a href="{{ route('events.show', $checklist->event) }}" class="text-decoration-none">
                                    <i class="fas fa-calendar text-primary me-2"></i>{{ $checklist->event->name }}
                                </a>
                                <span class="badge bg-light text-dark ms-2">{{ ucfirst($checklist->event->type) }}</span>
                            </div>
                        </div>
                        
                        @if($checklist->description)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả:</label>
                                <p class="mb-0">{{ $checklist->description }}</p>
                            </div>
                        @endif
                        
                        @if($checklist->due_date)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hạn thực hiện:</label>
                                <div>
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    {{ $checklist->due_date->format('d/m/Y') }}
                                    @if($checklist->due_time)
                                        lúc {{ $checklist->due_time->format('H:i') }}
                                    @endif
                                    
                                    @php
                                        $now = now();
                                        $dueDateTime = $checklist->due_date;
                                        if ($checklist->due_time) {
                                            $dueDateTime = $checklist->due_date->setTimeFromTimeString($checklist->due_time->format('H:i:s'));
                                        }
                                        $isOverdue = $dueDateTime < $now && !$checklist->is_completed;
                                        $daysLeft = $now->diffInDays($dueDateTime, false);
                                    @endphp
                                    
                                    @if($isOverdue)
                                        <span class="badge bg-danger ms-2">
                                            <i class="fas fa-exclamation-triangle"></i> Quá hạn {{ abs($daysLeft) }} ngày
                                        </span>
                                    @elseif($daysLeft <= 1 && !$checklist->is_completed)
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-clock"></i> Sắp hết hạn
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái:</label>
                            <div>
                                @if($checklist->is_completed)
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check-circle"></i> Hoàn thành
                                    </span>
                                    @if($checklist->completed_at)
                                        <small class="text-muted ms-2">
                                            ({{ $checklist->completed_at->format('d/m/Y H:i') }})
                                        </small>
                                    @endif
                                @else
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-clock"></i> Đang chờ
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Độ ưu tiên:</label>
                            <div>
                                <span class="badge bg-{{ $checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary') }} fs-6">
                                    @if($checklist->priority === 'high')
                                        <i class="fas fa-arrow-up"></i> Cao
                                    @elseif($checklist->priority === 'medium')
                                        <i class="fas fa-minus"></i> Trung bình
                                    @else
                                        <i class="fas fa-arrow-down"></i> Thấp
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        @if($checklist->assigned_to)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Người phụ trách:</label>
                                <div>
                                    <i class="fas fa-user text-muted me-2"></i>{{ $checklist->assigned_to }}
                                    @if($checklist->contact_info)
                                        <br><small class="text-muted">{{ $checklist->contact_info }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($checklist->estimated_duration)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian ước tính:</label>
                                <div>
                                    <i class="fas fa-hourglass text-muted me-2"></i>{{ $checklist->estimated_duration }} phút
                                    <small class="text-muted">({{ number_format($checklist->estimated_duration / 60, 1) }} giờ)</small>
                                </div>
                            </div>
                        @endif
                        
                        @if($checklist->reminder_before)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nhắc nhở trước:</label>
                                <div>
                                    <i class="fas fa-bell text-muted me-2"></i>
                                    @if($checklist->reminder_before >= 1440)
                                        {{ $checklist->reminder_before / 1440 }} ngày
                                    @elseif($checklist->reminder_before >= 60)
                                        {{ $checklist->reminder_before / 60 }} giờ
                                    @else
                                        {{ $checklist->reminder_before }} phút
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($checklist->notes)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ghi chú:</label>
                                <div class="border rounded p-2 bg-light">
                                    {{ $checklist->notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Công việc liên quan -->
        @if($relatedChecklists->count() > 0)
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="mb-0">Công việc liên quan trong sự kiện</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedChecklists as $related)
                            <div class="col-md-6 mb-3">
                                <div class="card border-start border-3 border-{{ $related->priority === 'high' ? 'danger' : ($related->priority === 'medium' ? 'warning' : 'secondary') }}">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('checklists.show', $related) }}" class="text-decoration-none">
                                                        {{ $related->title }}
                                                    </a>
                                                    @if($related->is_important)
                                                        <i class="fas fa-star text-warning ms-1"></i>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">
                                                    @if($related->due_date)
                                                        <i class="fas fa-clock me-1"></i>{{ $related->due_date->format('d/m') }}
                                                    @endif
                                                    @if($related->assigned_to)
                                                        <i class="fas fa-user ms-2 me-1"></i>{{ $related->assigned_to }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                @if($related->is_completed)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <div class="col-lg-4">
        <!-- Thống kê nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Thống kê nhanh</h6>
            </div>
            <div class="card-body">
                @php
                    $progress = 0;
                    if ($checklist->is_completed) {
                        $progress = 100;
                    } elseif ($checklist->due_date) {
                        $totalDays = $checklist->created_at->diffInDays($checklist->due_date);
                        $passedDays = $checklist->created_at->diffInDays(now());
                        $progress = $totalDays > 0 ? min(100, ($passedDays / $totalDays) * 100) : 0;
                    }
                @endphp
                
                <div class="text-center mb-3">
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-{{ $checklist->is_completed ? 'success' : ($progress > 80 ? 'danger' : ($progress > 50 ? 'warning' : 'info')) }}" 
                             style="width: {{ $progress }}%"></div>
                    </div>
                    <small class="text-muted">Tiến độ thời gian: {{ number_format($progress, 1) }}%</small>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            @if($checklist->due_date)
                                @php
                                    $now = now();
                                    $dueDateTime = $checklist->due_date;
                                    if ($checklist->due_time) {
                                        $dueDateTime = $checklist->due_date->setTimeFromTimeString($checklist->due_time->format('H:i:s'));
                                    }
                                    $daysLeft = $now->diffInDays($dueDateTime, false);
                                @endphp
                                
                                <h5 class="mb-0 text-{{ $daysLeft < 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info') }}">
                                    {{ abs($daysLeft) }}
                                </h5>
                                <small class="text-muted">
                                    {{ $daysLeft < 0 ? 'Ngày quá hạn' : ($daysLeft == 0 ? 'Hôm nay' : 'Ngày còn lại') }}
                                </small>
                            @else
                                <h5 class="mb-0 text-muted">--</h5>
                                <small class="text-muted">Không đặt hạn</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-{{ $checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary') }}">
                            {{ ucfirst($checklist->priority) }}
                        </h5>
                        <small class="text-muted">Độ ưu tiên</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hành động nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Hành động nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(!$checklist->is_completed)
                        <button type="button" class="btn btn-success" onclick="toggleComplete({{ $checklist->id }}, true)">
                            <i class="fas fa-check-circle me-2"></i>Đánh dấu hoàn thành
                        </button>
                    @else
                        <button type="button" class="btn btn-warning" onclick="toggleComplete({{ $checklist->id }}, false)">
                            <i class="fas fa-undo me-2"></i>Đánh dấu chưa hoàn thành
                        </button>
                    @endif
                    
                    <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa công việc
                    </a>
                    
                    <button type="button" class="btn btn-info" onclick="duplicateChecklist({{ $checklist->id }})">
                        <i class="fas fa-copy me-2"></i>Nhân bản công việc
                    </button>
                    
                    <a href="{{ route('events.show', $checklist->event) }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-2"></i>Xem sự kiện
                    </a>
                    
                    <button type="button" class="btn btn-outline-secondary" onclick="exportChecklist()">
                        <i class="fas fa-download me-2"></i>Xuất báo cáo
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Thông tin hệ thống -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Thông tin hệ thống</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-plus text-muted me-1"></i>Tạo lúc:</span>
                        <span>{{ $checklist->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    @if($checklist->updated_at != $checklist->created_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-edit text-muted me-1"></i>Cập nhật cuối:</span>
                            <span>{{ $checklist->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    
                    @if($checklist->is_completed && $checklist->completed_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-check-circle text-success me-1"></i>Hoàn thành lúc:</span>
                            <span>{{ $checklist->completed_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-hashtag text-muted me-1"></i>ID:</span>
                        <span>{{ $checklist->id }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-bell text-muted me-1"></i>Thông báo:</span>
                        <span>
                            @if($checklist->send_notification)
                                <span class="badge bg-success">Bật</span>
                            @else
                                <span class="badge bg-secondary">Tắt</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h5>Bạn có chắc chắn muốn xóa công việc này?</h5>
                    <p class="text-muted">Hành động này không thể hoàn tác!</p>
                    
                    <div class="alert alert-warning text-start mt-3">
                        <strong>Thông tin công việc sẽ bị xóa:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Tên: {{ $checklist->title }}</li>
                            <li>Sự kiện: {{ $checklist->event->name }}</li>
                            @if($checklist->due_date)
                                <li>Hạn: {{ $checklist->due_date->format('d/m/Y') }}</li>
                            @endif
                            @if($checklist->assigned_to)
                                <li>Người phụ trách: {{ $checklist->assigned_to }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <form method="POST" action="{{ route('checklists.destroy', $checklist) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Xóa công việc
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle complete status
function toggleComplete(checklistId, isCompleted) {
    const action = isCompleted ? 'complete' : 'incomplete';
    const message = isCompleted ? 'Đánh dấu công việc đã hoàn thành?' : 'Đánh dấu công việc chưa hoàn thành?';
    
    if (confirm(message)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}/${action}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Duplicate checklist
function duplicateChecklist(checklistId) {
    if (confirm('Bạn có muốn nhân bản công việc này?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}/duplicate`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Export checklist report
function exportChecklist() {
    const checklist = {
        title: '{{ $checklist->title }}',
        event: '{{ $checklist->event->name }}',
        description: '{{ $checklist->description }}',
        priority: '{{ $checklist->priority }}',
        status: '{{ $checklist->is_completed ? "Hoàn thành" : "Đang chờ" }}',
        assigned_to: '{{ $checklist->assigned_to }}',
        due_date: '{{ $checklist->due_date ? $checklist->due_date->format("d/m/Y") : "" }}',
        due_time: '{{ $checklist->due_time ? $checklist->due_time->format("H:i") : "" }}',
        estimated_duration: '{{ $checklist->estimated_duration }}',
        reminder_before: '{{ $checklist->reminder_before }}',
        notes: '{{ $checklist->notes }}',
        created_at: '{{ $checklist->created_at->format("d/m/Y H:i") }}',
        updated_at: '{{ $checklist->updated_at->format("d/m/Y H:i") }}'
    };
    
    let content = `BÁO CÁO CÔNG VIỆC\n`;
    content += `===================\n\n`;
    content += `Tên công việc: ${checklist.title}\n`;
    content += `Sự kiện: ${checklist.event}\n`;
    
    if (checklist.description) {
        content += `Mô tả: ${checklist.description}\n`;
    }
    
    content += `Độ ưu tiên: ${checklist.priority === 'high' ? 'Cao' : checklist.priority === 'medium' ? 'Trung bình' : 'Thấp'}\n`;
    content += `Trạng thái: ${checklist.status}\n`;
    
    if (checklist.assigned_to) {
        content += `Người phụ trách: ${checklist.assigned_to}\n`;
    }
    
    if (checklist.due_date) {
        content += `Hạn thực hiện: ${checklist.due_date}`;
        if (checklist.due_time) {
            content += ` lúc ${checklist.due_time}`;
        }
        content += `\n`;
    }
    
    if (checklist.estimated_duration) {
        content += `Thời gian ước tính: ${checklist.estimated_duration} phút\n`;
    }
    
    if (checklist.reminder_before) {
        content += `Nhắc nhở trước: ${checklist.reminder_before} phút\n`;
    }
    
    if (checklist.notes) {
        content += `Ghi chú: ${checklist.notes}\n`;
    }
    
    content += `\n--- THÔNG TIN HỆ THỐNG ---\n`;
    content += `Tạo lúc: ${checklist.created_at}\n`;
    content += `Cập nhật cuối: ${checklist.updated_at}\n`;
    content += `\n--- HẾT BÁO CÁO ---`;
    
    // Create and download file
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `bao-cao-cong-viec-${checklist.title.toLowerCase().replace(/\s+/g, '-')}-${new Date().getTime()}.txt`;
    link.click();
    
    // Show success message
    showToast('Đã xuất báo cáo công việc thành công!', 'success');
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}
</script>
@endpush