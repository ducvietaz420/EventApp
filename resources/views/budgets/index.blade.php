@extends('layouts.app')

@section('title', 'Quản lý ngân sách')
@section('page-title', 'Quản lý ngân sách')

@section('page-actions')
    <a href="{{ route('budgets.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Thêm ngân sách
    </a>
@endsection

@section('content')
<!-- Bộ lọc -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('budgets.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Tìm theo mô tả...">
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">Danh mục</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Tất cả</option>
                    <option value="venue" {{ request('category') === 'venue' ? 'selected' : '' }}>Địa điểm</option>
                    <option value="catering" {{ request('category') === 'catering' ? 'selected' : '' }}>Catering</option>
                    <option value="decoration" {{ request('category') === 'decoration' ? 'selected' : '' }}>Trang trí</option>
                    <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Thiết bị</option>
                    <option value="marketing" {{ request('category') === 'marketing' ? 'selected' : '' }}>Marketing</option>
                    <option value="staff" {{ request('category') === 'staff' ? 'selected' : '' }}>Nhân sự</option>
                    <option value="transportation" {{ request('category') === 'transportation' ? 'selected' : '' }}>Vận chuyển</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Khác</option>
                </select>
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
                <label for="min_amount" class="form-label">Từ (VNĐ)</label>
                <input type="number" class="form-control" id="min_amount" name="min_amount" 
                       value="{{ request('min_amount') }}" placeholder="0">
            </div>
            <div class="col-md-2">
                <label for="max_amount" class="form-label">Đến (VNĐ)</label>
                <input type="number" class="form-control" id="max_amount" name="max_amount" 
                       value="{{ request('max_amount') }}" placeholder="999999999">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($budgets->sum('estimated_cost'), 0, ',', '.') }}</h4>
                <p class="mb-0">Tổng ngân sách (VNĐ)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($budgets->sum('actual_cost'), 0, ',', '.') }}</h4>
                <p class="mb-0">Đã chi tiêu (VNĐ)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4>{{ number_format($budgets->sum('estimated_cost') - $budgets->sum('actual_cost'), 0, ',', '.') }}</h4>
                <p class="mb-0">Còn lại (VNĐ)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4>{{ $budgets->count() }}</h4>
                <p class="mb-0">Tổng số khoản</p>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách ngân sách -->
<div class="card shadow">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Danh sách ngân sách ({{ $budgets->total() }} kết quả)</h6>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" onclick="exportBudgets()">
                    <i class="fas fa-download me-1"></i>Xuất Excel
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($budgets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sự kiện</th>
                            <th>Danh mục</th>
                            <th>Mô tả</th>
                            <th class="text-end">Ngân sách</th>
                            <th class="text-end">Đã chi</th>
                            <th class="text-end">Còn lại</th>
                            <th class="text-center">Tiến độ</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($budgets as $budget)
                            @php
                                $remaining = $budget->estimated_cost - $budget->actual_cost;
                                $percentage = $budget->estimated_cost > 0 ? ($budget->actual_cost / $budget->estimated_cost) * 100 : 0;
                                $progressClass = $percentage > 100 ? 'bg-danger' : ($percentage > 80 ? 'bg-warning' : 'bg-success');
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('events.show', $budget->event_id) }}" class="text-decoration-none">
                                        {{ $budget->event->name }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $budget->event->type_display }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $budget->category_display }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $budget->description }}</div>
                                  
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($budget->estimated_cost, 0, ',', '.') }}</strong>
                                    <small class="text-muted d-block">VNĐ</small>
                                </td>
                                <td class="text-end">
                                    <span class="{{ $percentage > 100 ? 'text-danger' : 'text-primary' }}">
                                        <strong>{{ number_format($budget->actual_cost, 0, ',', '.') }}</strong>
                                    </span>
                                    <small class="text-muted d-block">VNĐ</small>
                                </td>
                                <td class="text-end">
                                    <span class="{{ $remaining < 0 ? 'text-danger' : 'text-success' }}">
                                        <strong>{{ number_format($remaining, 0, ',', '.') }}</strong>
                                    </span>
                                    <small class="text-muted d-block">VNĐ</small>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 8px; width: 60px; margin: 0 auto;">
                                        <div class="progress-bar {{ $progressClass }}" 
                                             style="width: {{ min($percentage, 100) }}%"
                                             title="{{ round($percentage, 1) }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ round($percentage, 1) }}%</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('budgets.show', $budget->id) }}" 
                                           class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('budgets.edit', $budget->id) }}" 
                                           class="btn btn-outline-warning" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteBudget({{ $budget->id }}, '{{ $budget->description }}')" 
                                                title="Xóa">
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
            <div class="card-footer">
                {{ $budgets->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy ngân sách nào</h5>
                <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tạo ngân sách mới.</p>
                <a href="{{ route('budgets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo ngân sách đầu tiên
                </a>
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
                <p>Bạn có chắc chắn muốn xóa khoản ngân sách <strong id="budgetDescription"></strong>?</p>
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
function deleteBudget(budgetId, description) {
    document.getElementById('budgetDescription').textContent = description;
    document.getElementById('deleteForm').action = `/budgets/${budgetId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function exportBudgets() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `{{ route('budgets.index') }}?${params.toString()}`;
}

// Auto-submit form when filters change
document.querySelectorAll('#category, #event_id').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});

// Clear filters
function clearFilters() {
    window.location.href = '{{ route('budgets.index') }}';
}

// Add clear filters button if any filter is active
if (window.location.search) {
    const clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.className = 'btn btn-outline-secondary';
    clearBtn.innerHTML = '<i class="fas fa-times me-1"></i>Xóa bộ lọc';
    clearBtn.onclick = clearFilters;
    
    const actionsDiv = document.querySelector('.card-header .btn-group');
    actionsDiv.appendChild(clearBtn);
}
</script>
@endpush