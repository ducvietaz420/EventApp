@extends('layouts.app')

@section('title', 'Chỉnh sửa công việc')
@section('page-title', 'Chỉnh sửa công việc: ' . $checklist->title)

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('checklists.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
        <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-outline-info">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
        <button type="button" class="btn btn-info" onclick="previewChecklist()">
            <i class="fas fa-eye me-2"></i>Xem trước
        </button>
        <button type="button" class="btn btn-warning" onclick="resetForm()">
            <i class="fas fa-undo me-2"></i>Khôi phục
        </button>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Form chỉnh sửa công việc -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Thông tin công việc</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('checklists.update', $checklist) }}" id="checklistForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Sự kiện <span class="text-danger">*</span></label>
                                <select class="form-select @error('event_id') is-invalid @enderror" 
                                        id="event_id" name="event_id" required>
                                    <option value="">Chọn sự kiện</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" 
                                                {{ old('event_id', $checklist->event_id) == $event->id ? 'selected' : '' }}
                                                data-type="{{ $event->type }}" data-date="{{ $event->event_date }}">
                                            {{ $event->name }} ({{ ucfirst($event->type) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('event_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tên công việc <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $checklist->title) }}" required 
                                       placeholder="Nhập tên công việc...">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả công việc</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Mô tả chi tiết về công việc...">{{ old('description', $checklist->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Ngày hạn</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" 
                                       value="{{ old('due_date', $checklist->due_date ? $checklist->due_date->format('Y-m-d') : '') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="due_time" class="form-label">Giờ hạn</label>
                                <input type="time" class="form-control @error('due_time') is-invalid @enderror" 
                                       id="due_time" name="due_time" 
                                       value="{{ old('due_time', $checklist->due_time ? $checklist->due_time->format('H:i') : '') }}">
                                @error('due_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Độ ưu tiên</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority">
                                    <option value="low" {{ old('priority', $checklist->priority) === 'low' ? 'selected' : '' }}>Thấp</option>
                                    <option value="medium" {{ old('priority', $checklist->priority) === 'medium' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="high" {{ old('priority', $checklist->priority) === 'high' ? 'selected' : '' }}>Cao</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Người phụ trách</label>
                                <input type="text" class="form-control @error('assigned_to') is-invalid @enderror" 
                                       id="assigned_to" name="assigned_to" 
                                       value="{{ old('assigned_to', $checklist->assigned_to) }}" 
                                       placeholder="Nhập tên người phụ trách...">
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_info" class="form-label">Thông tin liên hệ</label>
                                <input type="text" class="form-control @error('contact_info') is-invalid @enderror" 
                                       id="contact_info" name="contact_info" 
                                       value="{{ old('contact_info', $checklist->contact_info) }}" 
                                       placeholder="Email, số điện thoại...">
                                @error('contact_info')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estimated_duration" class="form-label">Thời gian ước tính (phút)</label>
                                <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                       id="estimated_duration" name="estimated_duration" 
                                       value="{{ old('estimated_duration', $checklist->estimated_duration) }}" 
                                       min="1" placeholder="60">
                                @error('estimated_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="reminder_before" class="form-label">Nhắc nhở trước (phút)</label>
                                <select class="form-select @error('reminder_before') is-invalid @enderror" 
                                        id="reminder_before" name="reminder_before">
                                    <option value="">Không nhắc nhở</option>
                                    <option value="15" {{ old('reminder_before', $checklist->reminder_before) == '15' ? 'selected' : '' }}>15 phút</option>
                                    <option value="30" {{ old('reminder_before', $checklist->reminder_before) == '30' ? 'selected' : '' }}>30 phút</option>
                                    <option value="60" {{ old('reminder_before', $checklist->reminder_before) == '60' ? 'selected' : '' }}>1 giờ</option>
                                    <option value="120" {{ old('reminder_before', $checklist->reminder_before) == '120' ? 'selected' : '' }}>2 giờ</option>
                                    <option value="1440" {{ old('reminder_before', $checklist->reminder_before) == '1440' ? 'selected' : '' }}>1 ngày</option>
                                </select>
                                @error('reminder_before')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="pending" {{ old('status', $checklist->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                    <option value="in_progress" {{ old('status', $checklist->status ?? 'pending') === 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                                    <option value="completed" {{ old('status', $checklist->status ?? 'pending') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="2" 
                                  placeholder="Ghi chú thêm về công việc...">{{ old('notes', $checklist->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_important" 
                                       name="is_important" value="1" 
                                       {{ old('is_important', $checklist->is_important) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_important">
                                    <i class="fas fa-star text-warning me-1"></i>Công việc quan trọng
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_completed" 
                                       name="is_completed" value="1" 
                                       {{ old('is_completed', $checklist->is_completed) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_completed">
                                    <i class="fas fa-check-circle text-success me-1"></i>Đã hoàn thành
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="send_notification" 
                                       name="send_notification" value="1" 
                                       {{ old('send_notification', $checklist->send_notification ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_notification">
                                    <i class="fas fa-bell text-info me-1"></i>Gửi thông báo
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button type="button" class="btn btn-warning" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Khôi phục
                        </button>
                        <button type="button" class="btn btn-info" onclick="previewChecklist()">
                            <i class="fas fa-eye me-2"></i>Xem trước
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật công việc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Thông tin nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Thông tin nhanh</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="mb-0">
                                <span class="badge bg-{{ $checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($checklist->priority) }}
                                </span>
                            </h5>
                            <small class="text-muted">Độ ưu tiên</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0">
                            @if($checklist->is_completed)
                                <span class="badge bg-success">Hoàn thành</span>
                            @else
                                <span class="badge bg-warning">Đang chờ</span>
                            @endif
                        </h5>
                        <small class="text-muted">Trạng thái</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-calendar text-muted me-1"></i>Sự kiện:</span>
                        <span>{{ $checklist->event->name }}</span>
                    </div>
                    
                    @if($checklist->due_date)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-clock text-muted me-1"></i>Hạn:</span>
                            <span>
                                {{ $checklist->due_date->format('d/m/Y') }}
                                @if($checklist->due_time)
                                    {{ $checklist->due_time->format('H:i') }}
                                @endif
                            </span>
                        </div>
                    @endif
                    
                    @if($checklist->assigned_to)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-user text-muted me-1"></i>Phụ trách:</span>
                            <span>{{ $checklist->assigned_to }}</span>
                        </div>
                    @endif
                    
                    @if($checklist->estimated_duration)
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-hourglass text-muted me-1"></i>Ước tính:</span>
                            <span>{{ $checklist->estimated_duration }} phút</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Lịch sử thay đổi -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Lịch sử thay đổi</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Tạo công việc</h6>
                            <p class="mb-0 small text-muted">
                                {{ $checklist->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                    
                    @if($checklist->updated_at != $checklist->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Cập nhật cuối</h6>
                                <p class="mb-0 small text-muted">
                                    {{ $checklist->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @endif
                    
                    @if($checklist->is_completed && $checklist->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Hoàn thành</h6>
                                <p class="mb-0 small text-muted">
                                    {{ $checklist->completed_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem trước -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem trước công việc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Cập nhật công việc</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
// Store original form data
let originalFormData = {};

// Capture original form data on page load
document.addEventListener('DOMContentLoaded', function() {
    captureOriginalData();
    
    // Track form changes
    const form = document.getElementById('checklistForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', trackChanges);
        input.addEventListener('input', trackChanges);
    });
    
    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
        }
    });
});

// Capture original form data
function captureOriginalData() {
    const formData = new FormData(document.getElementById('checklistForm'));
    originalFormData = Object.fromEntries(formData.entries());
    
    // Handle checkboxes separately
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        originalFormData[checkbox.name] = checkbox.checked ? '1' : '0';
    });
}

// Track form changes
function trackChanges() {
    const hasChanges = hasUnsavedChanges();
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (hasChanges) {
        submitBtn.classList.add('btn-warning');
        submitBtn.classList.remove('btn-primary');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Lưu thay đổi';
    } else {
        submitBtn.classList.add('btn-primary');
        submitBtn.classList.remove('btn-warning');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Cập nhật công việc';
    }
}

// Check if there are unsaved changes
function hasUnsavedChanges() {
    const currentFormData = new FormData(document.getElementById('checklistForm'));
    const currentData = Object.fromEntries(currentFormData.entries());
    
    // Handle checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        currentData[checkbox.name] = checkbox.checked ? '1' : '0';
    });
    
    // Compare with original data
    for (const key in originalFormData) {
        if (originalFormData[key] !== (currentData[key] || '')) {
            return true;
        }
    }
    
    for (const key in currentData) {
        if ((originalFormData[key] || '') !== currentData[key]) {
            return true;
        }
    }
    
    return false;
}

// Reset form to original values
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn khôi phục về giá trị ban đầu?')) {
        Object.keys(originalFormData).forEach(key => {
            const element = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = originalFormData[key] === '1';
                } else {
                    element.value = originalFormData[key] || '';
                }
            }
        });
        
        trackChanges();
        showToast('Đã khôi phục về giá trị ban đầu!', 'success');
    }
}

// Preview checklist
function previewChecklist() {
    const formData = new FormData(document.getElementById('checklistForm'));
    const data = Object.fromEntries(formData.entries());
    
    // Get event name
    const eventSelect = document.getElementById('event_id');
    const eventName = eventSelect.options[eventSelect.selectedIndex].text;
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Thông tin cơ bản</h6>
                <table class="table table-sm">
                    <tr><td><strong>Sự kiện:</strong></td><td>${eventName}</td></tr>
                    <tr><td><strong>Tên công việc:</strong></td><td>${data.title || 'Chưa nhập'}</td></tr>
                    <tr><td><strong>Độ ưu tiên:</strong></td><td>
                        <span class="badge bg-${data.priority === 'high' ? 'danger' : data.priority === 'medium' ? 'warning' : 'secondary'}">
                            ${data.priority === 'high' ? 'Cao' : data.priority === 'medium' ? 'Trung bình' : 'Thấp'}
                        </span>
                    </td></tr>
                    <tr><td><strong>Trạng thái:</strong></td><td>
                        <span class="badge bg-${data.status === 'completed' ? 'success' : data.status === 'in_progress' ? 'info' : 'warning'}">
                            ${data.status === 'completed' ? 'Hoàn thành' : data.status === 'in_progress' ? 'Đang thực hiện' : 'Đang chờ'}
                        </span>
                    </td></tr>
                    <tr><td><strong>Người phụ trách:</strong></td><td>${data.assigned_to || 'Chưa gán'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Thời gian</h6>
                <table class="table table-sm">
                    <tr><td><strong>Ngày hạn:</strong></td><td>${data.due_date ? new Date(data.due_date).toLocaleDateString('vi-VN') : 'Chưa đặt'}</td></tr>
                    <tr><td><strong>Giờ hạn:</strong></td><td>${data.due_time || 'Chưa đặt'}</td></tr>
                    <tr><td><strong>Thời gian ước tính:</strong></td><td>${data.estimated_duration ? data.estimated_duration + ' phút' : 'Chưa ước tính'}</td></tr>
                    <tr><td><strong>Nhắc nhở trước:</strong></td><td>${data.reminder_before ? data.reminder_before + ' phút' : 'Không'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    if (data.description) {
        html += `
            <div class="mt-3">
                <h6>Mô tả</h6>
                <p class="border rounded p-2">${data.description}</p>
            </div>
        `;
    }
    
    if (data.notes) {
        html += `
            <div class="mt-3">
                <h6>Ghi chú</h6>
                <p class="border rounded p-2">${data.notes}</p>
            </div>
        `;
    }
    
    if (data.contact_info) {
        html += `
            <div class="mt-3">
                <h6>Thông tin liên hệ</h6>
                <p class="border rounded p-2">${data.contact_info}</p>
            </div>
        `;
    }
    
    const flags = [];
    if (data.is_important) flags.push('<span class="badge bg-warning"><i class="fas fa-star"></i> Quan trọng</span>');
    if (data.is_completed) flags.push('<span class="badge bg-success"><i class="fas fa-check-circle"></i> Hoàn thành</span>');
    if (data.send_notification) flags.push('<span class="badge bg-info"><i class="fas fa-bell"></i> Thông báo</span>');
    
    if (flags.length > 0) {
        html += `
            <div class="mt-3">
                <h6>Đặc điểm</h6>
                <div>${flags.join(' ')}</div>
            </div>
        `;
    }
    
    // Show changes
    if (hasUnsavedChanges()) {
        html += `
            <div class="mt-3">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Có thay đổi chưa được lưu!</strong>
                </div>
            </div>
        `;
    }
    
    document.getElementById('previewContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

// Submit form
function submitForm() {
    document.getElementById('checklistForm').submit();
}

// Form validation
document.getElementById('checklistForm').addEventListener('submit', function(e) {
    const eventId = document.getElementById('event_id').value;
    const title = document.getElementById('title').value.trim();
    const dueDate = document.getElementById('due_date').value;
    
    if (!eventId) {
        e.preventDefault();
        showToast('Vui lòng chọn sự kiện!', 'error');
        document.getElementById('event_id').focus();
        return;
    }
    
    if (!title) {
        e.preventDefault();
        showToast('Vui lòng nhập tên công việc!', 'error');
        document.getElementById('title').focus();
        return;
    }
    
    if (dueDate) {
        const today = new Date();
        const selectedDate = new Date(dueDate);
        
        if (selectedDate < today.setHours(0,0,0,0)) {
            if (!confirm('Ngày hạn đã qua. Bạn có chắc chắn muốn tiếp tục?')) {
                e.preventDefault();
                return;
            }
        }
    }
});

// Auto-suggest due date when event changes
document.getElementById('event_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const eventDate = selectedOption.getAttribute('data-date');
    
    if (eventDate && !document.getElementById('due_date').value) {
        const eventDateObj = new Date(eventDate);
        const suggestedDate = new Date(eventDateObj.getTime() - (7 * 24 * 60 * 60 * 1000)); // 1 week before
        document.getElementById('due_date').value = suggestedDate.toISOString().split('T')[0];
        
        showToast('Đã gợi ý ngày hạn dựa trên ngày sự kiện', 'info');
    }
});

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