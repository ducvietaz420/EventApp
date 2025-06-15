@extends('layouts.app')

@section('title', 'Báo cáo sự kiện')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Báo cáo sự kiện</h1>
                    <p class="text-muted">Quản lý và xem báo cáo tổng kết sự kiện</p>
                </div>
                <div>
                    @if($event ?? false)
                        <a href="{{ route('events.reports.create', $event) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo mới
                        </a>
                    @else
                        <a href="{{ route('event-reports.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo mới
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-chart-bar fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['total'] ?? 0 }}</h5>
                            <small class="text-muted">Tổng báo cáo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['published'] ?? 0 }}</h5>
                            <small class="text-muted">Đã xuất bản</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-edit fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['drafts'] ?? 0 }}</h5>
                            <small class="text-muted">Bản nháp</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-clock fa-lg text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['pending_review'] ?? 0 }}</h5>
                            <small class="text-muted">Chờ duyệt</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Tìm kiếm báo cáo...">
                </div>
                @if(!($event ?? false))
                <div class="col-md-3">
                    <label for="event_id" class="form-label">Sự kiện</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tất cả sự kiện</option>
                        @foreach($events ?? [] as $e)
                            <option value="{{ $e->id }}" {{ request('event_id') == $e->id ? 'selected' : '' }}>
                                {{ $e->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2">
                    <label for="report_type" class="form-label">Loại báo cáo</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="">Tất cả</option>
                        <option value="financial" {{ request('report_type') === 'financial' ? 'selected' : '' }}>Tài chính</option>
                        <option value="summary" {{ request('report_type') === 'summary' ? 'selected' : '' }}>Tổng quan</option>
                        <option value="attendance" {{ request('report_type') === 'attendance' ? 'selected' : '' }}>Tham dự</option>
                        <option value="feedback" {{ request('report_type') === 'feedback' ? 'selected' : '' }}>Phản hồi</option>
                        <option value="final" {{ request('report_type') === 'final' ? 'selected' : '' }}>Tổng kết</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>Lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reports List -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Danh sách báo cáo</h6>
        </div>
        <div class="card-body">
            @if(($reports ?? collect())->count() > 0)
                <div class="row">
                    @foreach($reports as $report)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-start border-3 border-{{ $report->status === 'published' ? 'success' : ($report->status === 'draft' ? 'warning' : 'info') }}">
                                <div class="card-header d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-{{ $report->report_type === 'financial' ? 'primary' : ($report->report_type === 'final' ? 'success' : 'secondary') }} me-2">
                                            {{ ucfirst($report->report_type) }}
                                        </span>
                                        <span class="badge bg-{{ $report->status === 'published' ? 'success' : ($report->status === 'draft' ? 'warning' : 'info') }}">
                                            {{ $report->status === 'published' ? 'Đã xuất bản' : ($report->status === 'draft' ? 'Bản nháp' : 'Chờ duyệt') }}
                                        </span>
                                    </div>
                                    @if($report->success_score ?? false)
                                        <div class="text-end">
                                            <small class="text-muted">{{ number_format($report->success_score, 1) }}% thành công</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('event-reports.show', $report) }}" class="text-decoration-none">
                                            {{ $report->title }}
                                        </a>
                                    </h6>
                                    @if($report->summary)
                                        <p class="card-text text-muted small">{{ Str::limit($report->summary, 100) }}</p>
                                    @endif
                                    
                                    @if($report->event)
                                        <div class="mb-2">
                                            <small class="text-muted">Sự kiện:</small><br>
                                            <a href="{{ route('events.show', $report->event) }}" class="text-decoration-none">
                                                {{ $report->event->name }}
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if($report->roi_percentage ?? false)
                                        <div class="mb-2">
                                            <small class="text-muted">ROI:</small>
                                            <span class="badge bg-{{ $report->roi_percentage > 0 ? 'success' : 'danger' }}">
                                                {{ number_format($report->roi_percentage, 1) }}%
                                            </span>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>{{ $report->created_at->format('d/m/Y') }}
                                        </small>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($report->status === 'draft')
                                                <a href="{{ route('event-reports.edit', $report) }}" class="btn btn-outline-warning" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('event-reports.show', $report) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($report->status === 'published')
                                                <a href="{{ route('event-reports.exportPdf', $report) }}" class="btn btn-outline-success" title="Xuất PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if(method_exists($reports, 'links'))
                    <div class="d-flex justify-content-center">
                        {{ $reports->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Chưa có báo cáo nào</h6>
                    <p class="text-muted">Hãy tạo báo cáo đầu tiên cho sự kiện của bạn!</p>
                    @if($event ?? false)
                        <a href="{{ route('events.reports.create', $event) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo đầu tiên
                        </a>
                    @else
                        <a href="{{ route('event-reports.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo đầu tiên
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Generate Auto Report Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo báo cáo tự động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_event_id" class="form-label">Sự kiện *</label>
                        <select class="form-select" id="modal_event_id" name="event_id" required>
                            <option value="">Chọn sự kiện...</option>
                            @foreach($events ?? [] as $e)
                                <option value="{{ $e->id }}">{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_report_type" class="form-label">Loại báo cáo *</label>
                        <select class="form-select" id="modal_report_type" name="report_type" required>
                            <option value="">Chọn loại báo cáo...</option>
                            <option value="summary">Báo cáo tổng quan</option>
                            <option value="financial">Báo cáo tài chính</option>
                            <option value="final">Báo cáo tổng kết</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-magic me-2"></i>Tạo báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Auto submit form when filters change
document.querySelectorAll('select[name="event_id"], select[name="report_type"], select[name="status"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});

// Handle generate auto report
document.getElementById('modal_event_id').addEventListener('change', function() {
    const eventId = this.value;
    if (eventId) {
        document.getElementById('generateForm').action = `/events/${eventId}/reports/generate`;
    }
});
</script>
@endsection 