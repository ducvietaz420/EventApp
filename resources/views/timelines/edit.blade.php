@extends('layouts.app')

@section('title', 'Chỉnh sửa mốc thời gian')
@section('page-title', 'Chỉnh sửa mốc thời gian')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('timelines.show', $timeline) }}" class="btn btn-info">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
        <a href="{{ route('timelines.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Chỉnh sửa thông tin mốc thời gian</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('timelines.update', $timeline) }}" method="POST" id="timelineForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Sự kiện <span class="text-danger">*</span></label>
                                <select class="form-select @error('event_id') is-invalid @enderror" id="event_id" name="event_id" required>
                                    <option value="">Chọn sự kiện</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" 
                                                {{ old('event_id', $timeline->event_id) == $event->id ? 'selected' : '' }}
                                                data-type="{{ $event->type }}" data-date="{{ $event->event_date }}">
                                            {{ $event->name }} ({{ $event->type_display }})
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
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $timeline->title) }}" required
                                       placeholder="Nhập tiêu đề mốc thời gian">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3"
                                  placeholder="Mô tả chi tiết về mốc thời gian này">{{ old('description', $timeline->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" 
                                       value="{{ old('start_time', $timeline->start_time ? $timeline->start_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" 
                                       value="{{ old('end_time', $timeline->end_time ? $timeline->end_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estimated_duration" class="form-label">Thời lượng dự kiến (phút)</label>
                                <input type="number" class="form-control @error('estimated_duration') is-invalid @enderror" 
                                       id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $timeline->estimated_duration) }}" min="1"
                                       placeholder="Ví dụ: 60">
                                @error('estimated_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Độ ưu tiên</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                    <option value="low" {{ old('priority', $timeline->priority) === 'low' ? 'selected' : '' }}>Thấp</option>
                                    <option value="medium" {{ old('priority', $timeline->priority) === 'medium' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="high" {{ old('priority', $timeline->priority) === 'high' ? 'selected' : '' }}>Cao</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Địa điểm</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $timeline->location) }}"
                                       placeholder="Địa điểm thực hiện">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="responsible_person" class="form-label">Người phụ trách</label>
                                <input type="text" class="form-control @error('responsible_person') is-invalid @enderror" 
                                       id="responsible_person" name="responsible_person" value="{{ old('responsible_person', $timeline->responsible_person) }}"
                                       placeholder="Tên người phụ trách">
                                @error('responsible_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_milestone" class="form-label">Mốc quan trọng</label>
                                <div class="form-check">
                                    <input class="form-check-input @error('is_milestone') is-invalid @enderror" 
                                           type="checkbox" value="1" id="is_milestone" name="is_milestone"
                                           {{ old('is_milestone', $timeline->is_milestone) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_milestone">
                                        Đây là mốc quan trọng
                                    </label>
                                    @error('is_milestone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reminder_before" class="form-label">Nhắc nhở trước (phút)</label>
                                <select class="form-select @error('reminder_before') is-invalid @enderror" id="reminder_before" name="reminder_before">
                                    <option value="">Không nhắc nhở</option>
                                    <option value="15" {{ old('reminder_before', $timeline->reminder_before) == '15' ? 'selected' : '' }}>15 phút</option>
                                    <option value="30" {{ old('reminder_before', $timeline->reminder_before) == '30' ? 'selected' : '' }}>30 phút</option>
                                    <option value="60" {{ old('reminder_before', $timeline->reminder_before) == '60' ? 'selected' : '' }}>1 giờ</option>
                                    <option value="120" {{ old('reminder_before', $timeline->reminder_before) == '120' ? 'selected' : '' }}>2 giờ</option>
                                    <option value="1440" {{ old('reminder_before', $timeline->reminder_before) == '1440' ? 'selected' : '' }}>1 ngày</option>
                                    <option value="2880" {{ old('reminder_before', $timeline->reminder_before) == '2880' ? 'selected' : '' }}>2 ngày</option>
                                    <option value="10080" {{ old('reminder_before', $timeline->reminder_before) == '10080' ? 'selected' : '' }}>1 tuần</option>
                                </select>
                                @error('reminder_before')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending" {{ old('status', $timeline->status) === 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                    <option value="in_progress" {{ old('status', $timeline->status) === 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                                    <option value="completed" {{ old('status', $timeline->status) === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
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
                                  id="notes" name="notes" rows="3"
                                  placeholder="Ghi chú thêm về mốc thời gian này">{{ old('notes', $timeline->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Checkbox options -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_milestone" name="is_milestone" 
                                       value="1" {{ old('is_milestone', $timeline->is_milestone) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_milestone">
                                    Đây là mốc quan trọng
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" 
                                       value="1" {{ old('send_notification', $timeline->send_notification ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_notification">
                                    Gửi thông báo khi đến hạn
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('timelines.show', $timeline) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Khôi phục
                            </button>
                            <button type="button" class="btn btn-outline-info me-2" onclick="previewTimeline()">
                                <i class="fas fa-eye me-2"></i>Xem trước
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật mốc thời gian
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Thống kê nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Thông tin mốc thời gian</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="mb-0 text-{{ $timeline->is_completed ? 'success' : ($timeline->due_date && $timeline->due_date->isPast() ? 'danger' : 'warning') }}">
                                @if($timeline->is_completed)
                                    <i class="fas fa-check-circle"></i>
                                @elseif($timeline->due_date && $timeline->due_date->isPast())
                                    <i class="fas fa-exclamation-triangle"></i>
                                @else
                                    <i class="fas fa-clock"></i>
                                @endif
                            </h5>
                            <small class="text-muted">Trạng thái</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-{{ $timeline->priority === 'high' ? 'danger' : ($timeline->priority === 'medium' ? 'warning' : 'secondary') }}">
                            @switch($timeline->priority)
                                @case('high')
                                    <i class="fas fa-flag"></i>
                                    @break
                                @case('medium')
                                    <i class="fas fa-flag"></i>
                                    @break
                                @default
                                    <i class="fas fa-flag"></i>
                            @endswitch
                        </h5>
                        <small class="text-muted">Độ ưu tiên</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-2">
                    <small class="text-muted">Sự kiện:</small><br>
                    <a href="{{ route('events.show', $timeline->event) }}" class="text-decoration-none">
                        {{ $timeline->event->name }}
                    </a>
                </div>
                
                @if($timeline->due_date)
                    <div class="mb-2">
                        <small class="text-muted">Ngày thực hiện:</small><br>
                        {{ $timeline->due_date->format('d/m/Y') }}
                        @if($timeline->due_time)
                            lúc {{ $timeline->due_time->format('H:i') }}
                        @endif
                        
                        @if($timeline->due_date->isToday())
                            <span class="badge bg-warning ms-1">Hôm nay</span>
                        @elseif($timeline->due_date->isTomorrow())
                            <span class="badge bg-info ms-1">Ngày mai</span>
                        @elseif($timeline->due_date->isPast() && !$timeline->is_completed)
                            <span class="badge bg-danger ms-1">Quá hạn</span>
                        @endif
                    </div>
                @endif
                
                @if($timeline->assigned_to)
                    <div class="mb-2">
                        <small class="text-muted">Người phụ trách:</small><br>
                        {{ $timeline->assigned_to }}
                        @if($timeline->contact_info)
                            <br><small class="text-muted">{{ $timeline->contact_info }}</small>
                        @endif
                    </div>
                @endif
                
                @if($timeline->location)
                    <div class="mb-2">
                        <small class="text-muted">Địa điểm:</small><br>
                        {{ $timeline->location }}
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Lịch sử thay đổi -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Lịch sử thay đổi</h6>
            </div>
            <div class="card-body">
                <div class="timeline-history">
                    <div class="history-item">
                        <div class="history-marker bg-primary"></div>
                        <div class="history-content">
                            <small class="text-muted">Tạo mốc thời gian</small><br>
                            <small class="text-muted">{{ $timeline->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($timeline->updated_at != $timeline->created_at)
                        <div class="history-item">
                            <div class="history-marker bg-warning"></div>
                            <div class="history-content">
                                <small class="text-muted">Cập nhật lần cuối</small><br>
                                <small class="text-muted">{{ $timeline->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($timeline->is_completed)
                        <div class="history-item">
                            <div class="history-marker bg-success"></div>
                            <div class="history-content">
                                <small class="text-muted">Hoàn thành</small><br>
                                <small class="text-muted">{{ $timeline->completed_at ? $timeline->completed_at->format('d/m/Y H:i') : 'Chưa xác định' }}</small>
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
                <h5 class="modal-title">Xem trước mốc thời gian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Cập nhật mốc thời gian</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-history {
    position: relative;
    padding-left: 20px;
}

.timeline-history::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.history-item {
    position: relative;
    margin-bottom: 15px;
}

.history-marker {
    position: absolute;
    left: -16px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.history-content {
    padding-left: 10px;
}
</style>
@endpush

@push('scripts')
<script>
// Store original form data
const originalFormData = new FormData(document.getElementById('timelineForm'));
const originalData = Object.fromEntries(originalFormData.entries());

// Track form changes
let hasChanges = false;

// Form validation
document.getElementById('timelineForm').addEventListener('submit', function(e) {
    const requiredFields = ['event_id', 'title', 'due_date'];
    let isValid = true;
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ các trường bắt buộc!');
    }
});

// Track changes
document.querySelectorAll('#timelineForm input, #timelineForm select, #timelineForm textarea').forEach(field => {
    field.addEventListener('input', function() {
        hasChanges = true;
    });
    field.addEventListener('change', function() {
        hasChanges = true;
    });
});

// Warn before leaving if there are unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (hasChanges) {
        e.preventDefault();
        e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
    }
});

// Reset form to original values
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn khôi phục form về trạng thái ban đầu?')) {
        Object.keys(originalData).forEach(key => {
            const field = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = originalData[key] === '1';
                } else {
                    field.value = originalData[key] || '';
                }
            }
        });
        hasChanges = false;
    }
}

// Preview timeline
function previewTimeline() {
    const formData = new FormData(document.getElementById('timelineForm'));
    const data = Object.fromEntries(formData.entries());
    
    const eventName = document.querySelector('#event_id option:checked').text;
    const priorityText = {
        'low': 'Thấp',
        'medium': 'Trung bình',
        'high': 'Cao'
    }[data.priority] || 'Trung bình';
    
    const statusText = {
        'pending': 'Đang chờ',
        'in_progress': 'Đang thực hiện',
        'completed': 'Hoàn thành'
    }[data.status] || 'Đang chờ';
    
    let html = `
        <div class="timeline-preview">
            <h6 class="fw-bold">${data.title || 'Chưa có tiêu đề'}</h6>
            <p class="text-muted mb-2">${data.description || 'Chưa có mô tả'}</p>
            
            <div class="row">
                <div class="col-md-6">
                    <strong>Sự kiện:</strong> ${eventName !== 'Chọn sự kiện' ? eventName : 'Chưa chọn'}<br>
                    <strong>Ngày:</strong> ${data.due_date ? new Date(data.due_date).toLocaleDateString('vi-VN') : 'Chưa xác định'}<br>
                    <strong>Thời gian:</strong> ${data.due_time || 'Cả ngày'}<br>
                    <strong>Thời lượng:</strong> ${data.duration ? data.duration + ' phút' : 'Chưa xác định'}
                </div>
                <div class="col-md-6">
                    <strong>Độ ưu tiên:</strong> <span class="badge bg-${
                        data.priority === 'high' ? 'danger' : 
                        data.priority === 'medium' ? 'warning' : 'secondary'
                    }">${priorityText}</span><br>
                    <strong>Trạng thái:</strong> <span class="badge bg-${
                        data.status === 'completed' ? 'success' : 
                        data.status === 'in_progress' ? 'primary' : 'warning'
                    }">${statusText}</span><br>
                    <strong>Địa điểm:</strong> ${data.location || 'Chưa xác định'}<br>
                    <strong>Người phụ trách:</strong> ${data.assigned_to || 'Chưa xác định'}
                </div>
            </div>
            
            ${data.notes ? `<div class="mt-3"><strong>Ghi chú:</strong><br>${data.notes}</div>` : ''}
            
            <div class="mt-3">
                ${data.is_milestone ? '<span class="badge bg-info me-2">Mốc quan trọng</span>' : ''}
                ${data.send_notification ? '<span class="badge bg-success me-2">Có thông báo</span>' : ''}
                ${data.reminder_before ? `<span class="badge bg-warning">Nhắc nhở trước ${data.reminder_before} phút</span>` : ''}
            </div>
            
            <div class="mt-3">
                <h6>So sánh với dữ liệu hiện tại:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Thay đổi:</strong>
                        <ul class="list-unstyled">
    `;
    
    // Compare changes
    const changes = [];
    Object.keys(data).forEach(key => {
        if (originalData[key] !== data[key]) {
            const fieldName = {
                'title': 'Tiêu đề',
                'description': 'Mô tả',
                'due_date': 'Ngày thực hiện',
                'due_time': 'Thời gian',
                'priority': 'Độ ưu tiên',
                'location': 'Địa điểm',
                'assigned_to': 'Người phụ trách',
                'notes': 'Ghi chú'
            }[key] || key;
            
            changes.push(`<li><small>${fieldName}: ${originalData[key] || 'Trống'} → ${data[key] || 'Trống'}</small></li>`);
        }
    });
    
    if (changes.length === 0) {
        html += '<li><small class="text-muted">Không có thay đổi</small></li>';
    } else {
        html += changes.join('');
    }
    
    html += `
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

function submitForm() {
    hasChanges = false; // Prevent beforeunload warning
    document.getElementById('timelineForm').submit();
}

// Auto-suggest due date when event changes
document.getElementById('event_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value && selectedOption.dataset.date) {
        const eventDate = new Date(selectedOption.dataset.date);
        const currentDueDate = new Date(document.getElementById('due_date').value);
        
        // Only suggest if current due date is empty or user confirms
        if (!document.getElementById('due_date').value || 
            confirm('Bạn có muốn cập nhật ngày thực hiện dựa trên ngày sự kiện mới?')) {
            
            const suggestedDate = new Date(eventDate);
            suggestedDate.setDate(suggestedDate.getDate() - 7); // 1 week before event
            
            document.getElementById('due_date').value = suggestedDate.toISOString().split('T')[0];
            hasChanges = true;
        }
    }
});
</script>
@endpush