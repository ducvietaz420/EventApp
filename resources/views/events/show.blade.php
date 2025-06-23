@extends('layouts.app')

@section('title', $event->name . ' - Chi tiết sự kiện')
@section('page-title', $event->name)

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('events.images.index', $event) }}" class="btn btn-info">
            <i class="fas fa-images me-2"></i>Quản lý ảnh
        </a>
        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa
        </a>
        <button type="button" class="btn btn-danger" onclick="deleteEvent({{ $event->id }}, '{{ $event->name }}')">
            <i class="fas fa-trash me-2"></i>Xóa
        </button>
        <a href="{{ route('events.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
@endsection

@section('content')
<!-- Thông tin tổng quan -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Thông tin cơ bản</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" style="width: 120px;">Loại sự kiện:</td>
                                <td><span class="badge bg-info">{{ $event->type_display }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trạng thái:</td>
                                <td>
                                    <span class="status-badge status-{{ $event->status }}">
                                        @switch($event->status)
                                            @case('planning')
                                                Đang lên kế hoạch
                                                @break
                                            @case('in_progress')
                                                Đang tiến hành
                                                @break
                                            @case('completed')
                                                Hoàn thành
                                                @break
                                            @case('cancelled')
                                                Đã hủy
                                                @break
                                            @default
                                                {{ ucfirst($event->status) }}
                                        @endswitch
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày diễn ra:</td>
                                <td>
                                    @if($event->event_date)
                                        <strong>{{ $event->event_date->format('d/m/Y') }}</strong>
                                    @else
                                        <span class="text-muted">Chưa xác định</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Địa điểm:</td>
                                <td>
                                    @if($event->venue)
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        {{ $event->venue }}
                                    @else
                                        <span class="text-muted">Chưa xác định</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Số khách mời:</td>
                                <td>
                                    @if($event->expected_guests)
                                        <i class="fas fa-users text-muted me-1"></i>
                                        {{ number_format($event->expected_guests) }} người
                                    @else
                                        <span class="text-muted">Chưa xác định</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Mô tả</h6>
                        <p class="text-muted">
                            {{ $event->description ?: 'Chưa có mô tả' }}
                        </p>
                        
                        @if($event->notes)
                            <h6 class="text-muted mb-2 mt-3">Ghi chú</h6>
                            <p class="text-muted">{{ $event->notes }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Thống kê nhanh -->
        <div class="card shadow mb-3">
            <div class="card-body text-center">
                <h6 class="text-muted mb-3">Tiến độ hoàn thành</h6>
                @php
                    $totalTasks = $event->checklists->count();
                    $completedTasks = $event->checklists->whereNotNull('completed_at')->count();
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp
                <div class="progress mb-3" style="height: 15px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $progress }}%" 
                         aria-valuenow="{{ $progress }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $progress }}%
                    </div>
                </div>
                <p class="mb-0">{{ $completedTasks }}/{{ $totalTasks }} nhiệm vụ hoàn thành</p>
            </div>
        </div>
        
        <!-- Ảnh sự kiện -->
        <div class="card shadow">
            <div class="card-body text-center">
                <h6 class="text-muted mb-3">Ảnh sự kiện</h6>
                <div class="row text-center">
                    <div class="col-12 mb-2">
                        <h5 class="text-primary mb-0">{{ $event->total_images }}</h5>
                        <small class="text-muted">Tổng số ảnh</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-success mb-0">{{ $event->total_nghiem_thu_images }}</h6>
                        <small class="text-muted">Nghiệm thu</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-info mb-0">{{ $event->total_thiet_ke_images }}</h6>
                        <small class="text-muted">Thiết kế</small>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('events.images.index', $event) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-images me-1"></i> Xem tất cả ảnh
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs chi tiết -->
<div class="card shadow">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="eventTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="checklist-tab" data-bs-toggle="tab" 
                        data-bs-target="#checklist" type="button" role="tab">
                    <i class="fas fa-check-square me-2"></i>Checklist
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="images-tab" data-bs-toggle="tab" 
                        data-bs-target="#images" type="button" role="tab">
                    <i class="fas fa-images me-2"></i>Ảnh sự kiện
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ai-suggestions-tab" data-bs-toggle="tab" 
                        data-bs-target="#ai-suggestions" type="button" role="tab">
                    <i class="fas fa-robot me-2"></i>AI Suggestions
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="eventTabsContent">
            <!-- Checklist Tab -->
            <div class="tab-pane fade show active" id="checklist" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Danh sách công việc</h6>
                    <a href="{{ route('events.checklists.create', $event->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Thêm công việc
                    </a>
                </div>
                
                @if($event->checklists->count() > 0)
                    @php
                        $groupedChecklists = $event->checklists->groupBy('category');
                    @endphp
                    
                    @foreach($groupedChecklists as $category => $checklists)
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-folder me-2"></i>{{ 
                                    match($category) {
                                        'venue' => 'Địa điểm',
                                        'catering' => 'Catering',
                                        'decoration' => 'Trang trí',
                                        'equipment' => 'Thiết bị',
                                        'marketing' => 'Marketing',
                                        'staff' => 'Nhân sự',
                                        'transportation' => 'Vận chuyển',
                                        'other' => 'Khác',
                                        default => ucfirst($category)
                                    }
                                }}
                            </h6>
                            
                            @foreach($checklists as $checklist)
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       {{ $checklist->completed_at ? 'checked' : '' }}
                                                       onchange="toggleComplete({{ $checklist->id }})">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 {{ $checklist->completed_at ? 'text-decoration-line-through text-muted' : '' }}">
                                                    {{ $checklist->title }}
                                                </h6>
                                                @if($checklist->description)
                                                    <p class="text-muted mb-1 small">{{ $checklist->description }}</p>
                                                @endif
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-{{ $checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($checklist->priority) }}
                                                    </span>
                                                    @if($checklist->due_date)
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $checklist->due_date->format('d/m/Y') }}
                                                        </small>
                                                    @endif
                                                    @if($checklist->completed_at)
                                                        <small class="text-success">
                                                            <i class="fas fa-check me-1"></i>
                                                            Hoàn thành {{ $checklist->completed_at->format('d/m/Y') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-square fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có công việc nào</p>
                        <a href="{{ route('events.checklists.create', $event->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo công việc đầu tiên
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- Images Tab -->
            <div class="tab-pane fade" id="images" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Ảnh sự kiện</h6>
                    <a href="{{ route('events.images.index', $event) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-images me-1"></i>Quản lý ảnh
                    </a>
                </div>
                
                @if($event->images->count() > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Ảnh Nghiệm Thu</h6>
                                </div>
                                <div class="card-body">
                                    @if($event->nghiemThuImages->count() > 0)
                                        <div class="row g-2">
                                            @foreach($event->nghiemThuImages->take(6) as $image)
                                                <div class="col-4">
                                                    <img src="{{ $image->file_url }}" 
                                                         alt="{{ $image->original_filename }}" 
                                                         class="img-fluid rounded"
                                                         style="width: 100%; height: 80px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($event->nghiemThuImages->count() > 6)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">và {{ $event->nghiemThuImages->count() - 6 }} ảnh khác</small>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có ảnh nghiệm thu</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-palette me-2"></i>Ảnh Thiết Kế</h6>
                                </div>
                                <div class="card-body">
                                    @if($event->thietKeImages->count() > 0)
                                        <div class="row g-2">
                                            @foreach($event->thietKeImages->take(6) as $image)
                                                <div class="col-4">
                                                    <img src="{{ $image->file_url }}" 
                                                         alt="{{ $image->original_filename }}" 
                                                         class="img-fluid rounded"
                                                         style="width: 100%; height: 80px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if($event->thietKeImages->count() > 6)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">và {{ $event->thietKeImages->count() - 6 }} ảnh khác</small>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có ảnh thiết kế</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('events.images.index', $event) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Xem tất cả ảnh ({{ $event->total_images }})
                        </a>
                        @if($event->images->count() > 0)
                            <a href="{{ route('events.images.download-zip', $event) }}" class="btn btn-outline-success">
                                <i class="fas fa-download me-1"></i>Tải ZIP
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có ảnh nào</p>
                        <a href="{{ route('events.images.index', $event) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm ảnh đầu tiên
                        </a>
                    </div>
                @endif
            </div>
            
            <!-- AI Suggestions Tab -->
            <div class="tab-pane fade" id="ai-suggestions" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">AI Suggestions</h6>
                    <form action="{{ route('events.ai-suggestions.generate', $event->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-robot me-1"></i>Tạo gợi ý mới
                        </button>
                    </form>
                </div>
                
                @if($event->aiSuggestions->count() > 0)
                    @foreach($event->aiSuggestions->sortByDesc('created_at') as $suggestion)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $suggestion->suggestion_type }}</h6>
                                        <span class="badge bg-{{ $suggestion->status === 'accepted' ? 'success' : ($suggestion->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($suggestion->status) }}
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $suggestion->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                <p class="mb-2">{{ $suggestion->content }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Độ tin cậy: {{ $suggestion->confidence_score }}% | 
                                        Model: {{ $suggestion->ai_model }}
                                    </small>
                                    @if($suggestion->status === 'pending')
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('events.ai-suggestions.accept', [$event->id, $suggestion->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('events.ai-suggestions.reject', [$event->id, $suggestion->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có gợi ý AI nào</p>
                        <form action="{{ route('events.ai-suggestions.generate', $event->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-robot me-2"></i>Tạo gợi ý đầu tiên
                            </button>
                        </form>
                    </div>
                @endif
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
                <p>Bạn có chắc chắn muốn xóa sự kiện <strong id="eventName"></strong>?</p>
                <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteEvent(eventId, eventName) {
    document.getElementById('eventName').textContent = eventName;
    document.getElementById('deleteForm').action = `/events/${eventId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleComplete(checklistId) {
    fetch(`/events/{{ $event->id }}/checklists/${checklistId}/complete`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        location.reload();
    });
}
</script>
@endpush