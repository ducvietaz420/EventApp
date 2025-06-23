@extends('layouts.app')

@section('title', 'Danh sách sự kiện')
@section('page-title', 'Quản lý sự kiện')

@section('page-actions')
    @auth
        @if(auth()->user()->hasPermission('events.create'))
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
            </a>
        @endif
    @endauth
@endsection

@section('content')
    <div class="card shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách sự kiện</h6>
                </div>
                <div class="col-auto">
                    <!-- Bộ lọc và tìm kiếm -->
                    <form method="GET" action="{{ route('events.index') }}" class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sự kiện..."
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <select name="status" class="form-select form-select-sm" style="width: 150px;"
                            onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Đang lên kế
                                hoạch</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang tiến
                                hành</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy
                            </option>
                        </select>

                        <select name="type" class="form-select form-select-sm" style="width: 150px;"
                            onchange="this.form.submit()">
                            <option value="">Tất cả loại</option>
                            <option value="conference" {{ request('type') === 'conference' ? 'selected' : '' }}>Hội nghị
                            </option>
                            <option value="wedding" {{ request('type') === 'wedding' ? 'selected' : '' }}>Đám cưới</option>
                            <option value="corporate" {{ request('type') === 'corporate' ? 'selected' : '' }}>Doanh nghiệp
                            </option>
                            <option value="workshop" {{ request('type') === 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="seminar" {{ request('type') === 'seminar' ? 'selected' : '' }}>Seminar</option>
                            <option value="party" {{ request('type') === 'party' ? 'selected' : '' }}>Tiệc</option>
                            <option value="exhibition" {{ request('type') === 'exhibition' ? 'selected' : '' }}>Triển lãm
                            </option>
                            <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Khác</option>
                        </select>

                        @if(request()->hasAny(['search', 'status', 'type']))
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if(isset($events) && $events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sự kiện</th>
                                <th>Loại</th>
                                <th>Ngày diễn ra</th>
                                <th>Địa điểm</th>
                                <th>Trạng thái</th>
                                <th>Tiến độ</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 35px; height: 35px;">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0">
                                                    <a href="{{ route('events.show', $event->id) }}" class="text-decoration-none">
                                                        {{ $event->name }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $event->type_display }}</span>
                                    </td>
                                    <td>
                                        @if($event->event_date)
                                            <div>
                                                <strong>{{ $event->event_date->format('d/m/Y') }}</strong><br>
                                                <!-- <small class="text-muted">{{ $event->event_date->format('H:i') }}</small> -->
                                            </div>
                                        @else
                                            <span class="text-muted">Chưa xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($event->venue)
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            {{ Str::limit($event->venue, 30) }}
                                        @else
                                            <span class="text-muted">Chưa xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('events.updateStatus', $event->id) }}" method="POST"
                                            class="status-update-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status"
                                                class="form-select form-select-sm status-badge status-{{ $event->status }}"
                                                onchange="this.form.submit()">
                                                <option value="planning" {{ $event->status === 'planning' ? 'selected' : '' }}>Đang
                                                    lên kế hoạch</option>
                                                <option value="confirmed" {{ $event->status === 'confirmed' ? 'selected' : '' }}>Đã
                                                    xác nhận</option>
                                                <option value="in_progress" {{ $event->status === 'in_progress' ? 'selected' : '' }}>
                                                    Đang tiến hành</option>
                                                <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>Hoàn
                                                    thành</option>
                                                <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>Đã
                                                    hủy</option>
                                            </select>
                                        </form>
                                    </td>
                                   
                                    <td>
                                        @if($event->checklists && $event->checklists->count() > 0)
                                            @php
                                                $totalTasks = $event->checklists->count();
                                                $completedTasks = $event->checklists->whereNotNull('completed_at')->count();
                                                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                            @endphp
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%"
                                                    aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $completedTasks }}/{{ $totalTasks }} nhiệm vụ</small>
                                        @else
                                            <span class="text-muted">Chưa có nhiệm vụ</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @auth
                                                @if(auth()->user()->hasPermission('events.view'))
                                                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-sm btn-outline-primary"
                                                        title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                
                                                @if(auth()->user()->hasPermission('events.edit'))
                                                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-outline-warning"
                                                        title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if(auth()->user()->hasPermission('events.delete'))
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                        onclick="deleteEvent({{ $event->id }}, '{{ $event->name }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            @endauth
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($events->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Hiển thị {{ $events->firstItem() }} đến {{ $events->lastItem() }}
                                trong tổng số {{ $events->total() }} sự kiện
                            </div>
                            <div>
                                {{ $events->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có sự kiện nào</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'type']))
                            Không tìm thấy sự kiện phù hợp với bộ lọc.
                        @else
                            Bạn chưa tạo sự kiện nào. Hãy tạo sự kiện đầu tiên!
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'type']))
                        <a href="{{ route('events.index') }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-times me-2"></i>Xóa bộ lọc
                        </a>
                    @endif
                    @auth
                        @if(auth()->user()->hasPermission('events.create'))
                            <a href="{{ route('events.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
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
    </script>
@endpush