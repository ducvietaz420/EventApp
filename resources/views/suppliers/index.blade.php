@extends('layouts.app')

@section('title', 'Quản lý nhà cung cấp')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Quản lý nhà cung cấp</h1>
                    <p class="text-muted">Quản lý thông tin các nhà cung cấp dịch vụ</p>
                </div>
                <div>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp
                    </a>
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
                            <i class="fas fa-truck fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['total'] ?? 0 }}</h5>
                            <small class="text-muted">Tổng nhà cung cấp</small>
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
                            <h5 class="mb-0">{{ $stats['verified'] ?? 0 }}</h5>
                            <small class="text-muted">Đã xác minh</small>
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
                            <i class="fas fa-star fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['preferred'] ?? 0 }}</h5>
                            <small class="text-muted">Ưu tiên</small>
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
                            <i class="fas fa-circle fa-lg text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $stats['available'] ?? 0 }}</h5>
                            <small class="text-muted">Khả dụng</small>
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
                           value="{{ request('search') }}" placeholder="Tìm kiếm nhà cung cấp...">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Loại dịch vụ</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tất cả</option>
                        <option value="catering" {{ request('type') === 'catering' ? 'selected' : '' }}>Catering</option>
                        <option value="decoration" {{ request('type') === 'decoration' ? 'selected' : '' }}>Trang trí</option>
                        <option value="photography" {{ request('type') === 'photography' ? 'selected' : '' }}>Chụp ảnh</option>
                        <option value="venue" {{ request('type') === 'venue' ? 'selected' : '' }}>Địa điểm</option>
                        <option value="entertainment" {{ request('type') === 'entertainment' ? 'selected' : '' }}>Giải trí</option>
                        <option value="transportation" {{ request('type') === 'transportation' ? 'selected' : '' }}>Vận chuyển</option>
                        <option value="flowers" {{ request('type') === 'flowers' ? 'selected' : '' }}>Hoa</option>
                        <option value="equipment" {{ request('type') === 'equipment' ? 'selected' : '' }}>Thiết bị</option>
                        <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                        <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Danh sách đen</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="verified" class="form-label">Xác minh</label>
                    <select class="form-select" id="verified" name="verified">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Đã xác minh</option>
                        <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Chưa xác minh</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Suppliers List -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Danh sách nhà cung cấp</h6>
        </div>
        <div class="card-body">
            @if(($suppliers ?? collect())->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nhà cung cấp</th>
                                <th>Loại dịch vụ</th>
                                <th>Liên hệ</th>
                                <th>Đánh giá</th>
                                <th>Ngân sách</th>
                                <th>Trạng thái</th>
                                <th width="150">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-building text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">
                                                    <a href="{{ route('suppliers.show', $supplier) }}" class="text-decoration-none">
                                                        {{ $supplier->name }}
                                                    </a>
                                                    @if($supplier->is_verified ?? false)
                                                        <i class="fas fa-check-circle text-success ms-1" title="Đã xác minh"></i>
                                                    @endif
                                                    @if($supplier->is_preferred ?? false)
                                                        <i class="fas fa-star text-warning ms-1" title="Ưu tiên"></i>
                                                    @endif
                                                </h6>
                                                @if($supplier->company_name)
                                                    <small class="text-muted">{{ $supplier->company_name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($supplier->type) }}</span>
                                    </td>
                                    <td>
                                        @if($supplier->contact_person)
                                            <div><i class="fas fa-user text-muted me-1"></i>{{ $supplier->contact_person }}</div>
                                        @endif
                                        @if($supplier->phone)
                                            <div><i class="fas fa-phone text-muted me-1"></i>{{ $supplier->phone }}</div>
                                        @endif
                                        @if($supplier->email)
                                            <div><i class="fas fa-envelope text-muted me-1"></i>{{ $supplier->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->rating > 0)
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= floor($supplier->rating))
                                                            <i class="fas fa-star text-warning"></i>
                                                        @elseif($i - 0.5 <= $supplier->rating)
                                                            <i class="fas fa-star-half-alt text-warning"></i>
                                                        @else
                                                            <i class="far fa-star text-warning"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <small class="text-muted">({{ $supplier->total_reviews }})</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Chưa có đánh giá</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->min_budget && $supplier->max_budget)
                                            <div>{{ number_format($supplier->min_budget, 0, ',', '.') }} - {{ number_format($supplier->max_budget, 0, ',', '.') }} VNĐ</div>
                                        @elseif($supplier->min_budget)
                                            <div>Từ {{ number_format($supplier->min_budget, 0, ',', '.') }} VNĐ</div>
                                        @else
                                            <span class="text-muted">Chưa xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($supplier->status) {
                                                'active' => 'success',
                                                'inactive' => 'warning',
                                                'blacklisted' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusText = match($supplier->status) {
                                                'active' => 'Hoạt động',
                                                'inactive' => 'Không hoạt động',
                                                'blacklisted' => 'Danh sách đen',
                                                default => 'Không xác định'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteSupplier({{ $supplier->id }})" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(method_exists($suppliers, 'links'))
                    <div class="d-flex justify-content-center">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Chưa có nhà cung cấp nào</h6>
                    <p class="text-muted">Hãy thêm nhà cung cấp đầu tiên!</p>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Thêm nhà cung cấp đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa nhà cung cấp này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function deleteSupplier(supplierId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/suppliers/${supplierId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Auto submit form when filters change
document.querySelectorAll('select[name="type"], select[name="status"], select[name="verified"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endsection 