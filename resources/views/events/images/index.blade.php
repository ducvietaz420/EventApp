@extends('layouts.app')

@section('title', 'Quản lý Hình ảnh - ' . $event->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Quản lý Hình ảnh</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Sự kiện</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('events.show', $event) }}">{{ $event->name }}</a></li>
                            <li class="breadcrumb-item active">Hình ảnh</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    @if($event->images->count() > 0)
                        <a href="{{ route('events.images.download-zip', $event) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Tải xuống ZIP
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin sự kiện -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-2">{{ $event->name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $event->venue ?? 'Chưa xác định' }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-{{ $event->status === 'completed' ? 'success' : ($event->status === 'in_progress' ? 'warning' : 'info') }} fs-6">
                                {{ ucfirst(str_replace('_', ' ', $event->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <i class="fas fa-images fa-2x text-info mb-2"></i>
                    <h4 class="fw-bold">{{ $event->images->count() }}</h4>
                    <p class="text-muted mb-0">Tổng số file</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h4 class="fw-bold">{{ $event->nghiemThuImages->count() }}</h4>
                    <p class="text-muted mb-0">Ảnh nghiệm thu</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <i class="fas fa-palette fa-2x text-warning mb-2"></i>
                    <h4 class="fw-bold">{{ $event->thietKeImages->count() }}</h4>
                    <p class="text-muted mb-0">File thiết kế</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <i class="fas fa-hdd fa-2x text-primary mb-2"></i>
                    <h4 class="fw-bold">{{ number_format($event->images->sum('file_size') / 1024 / 1024, 1) }} MB</h4>
                    <p class="text-muted mb-0">Tổng dung lượng</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-cloud-upload-alt me-2"></i>Tải lên file mới</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('events.images.upload', $event) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="image_type" class="form-label"><strong>Loại file *</strong></label>
                                <select class="form-select" id="image_type" name="image_type" required>
                                    <option value="">-- Chọn loại file --</option>
                                    <option value="nghiem_thu">Ảnh Nghiệm Thu</option>
                                    <option value="thiet_ke">File Thiết Kế</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="category" class="form-label"><strong>Danh mục</strong></label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">-- Chọn danh mục --</option>
                                    <option value="backdrop">Backdrop</option>
                                    <option value="led">Màn hình LED</option>
                                    <option value="san-khau">Sân khấu</option>
                                    <option value="standee">Standee</option>
                                    <option value="banner">Banner</option>
                                    <option value="invitation">Thiệp mời</option>
                                    <option value="menu">Menu</option>
                                    <option value="table-setup">Bàn tiệc</option>
                                    <option value="decoration">Trang trí</option>
                                    <option value="lighting">Ánh sáng</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="description" class="form-label"><strong>Mô tả</strong></label>
                                <textarea class="form-control" id="description" name="description" rows="2" placeholder="Nhập mô tả về file (tùy chọn)"></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="images" class="form-label"><strong>Chọn file *</strong></label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple accept=".jpg,.jpeg,.png,.gif,.ai,.psd,.eps,.svg,.pdf,.tiff,.bmp" required>
                                <div class="form-text">
                                    <strong>Các định dạng được hỗ trợ:</strong><br>
                                    • <strong>Ảnh:</strong> JPG, PNG, GIF, TIFF, BMP (tối đa 10MB/file)<br>
                                    • <strong>Thiết kế:</strong> AI, PSD, EPS, SVG, PDF (tối đa 50MB/file)<br>
                                    • Có thể chọn nhiều file cùng lúc
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-upload me-2"></i>Tải lên file
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="resetForm()">
                                    <i class="fas fa-times me-2"></i>Hủy bỏ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs cho các loại ảnh -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs nav-fill" id="imageTypeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        <i class="fas fa-th-large me-2"></i>Tất cả ({{ $event->images->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="nghiem-thu-tab" data-bs-toggle="tab" data-bs-target="#nghiem-thu" type="button" role="tab">
                        <i class="fas fa-check-circle me-2"></i>Ảnh Nghiệm Thu ({{ $event->nghiemThuImages->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="thiet-ke-tab" data-bs-toggle="tab" data-bs-target="#thiet-ke" type="button" role="tab">
                        <i class="fas fa-palette me-2"></i>File Thiết Kế ({{ $event->thietKeImages->count() }})
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="imageTypeTabsContent">
                <!-- Tất cả ảnh -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    @if($event->images->count() > 0)
                        <div class="row g-3">
                            @foreach($event->images->sortByDesc('created_at') as $image)
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    @include('events.images.partials.image-card', ['image' => $image])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có file nào</h5>
                            <p class="text-muted">Hãy tải lên file đầu tiên cho sự kiện này</p>
                        </div>
                    @endif
                </div>

                <!-- Ảnh nghiệm thu -->
                <div class="tab-pane fade" id="nghiem-thu" role="tabpanel">
                    @if($event->nghiemThuImages->count() > 0)
                        <div class="row g-3">
                            @foreach($event->nghiemThuImages->sortByDesc('created_at') as $image)
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    @include('events.images.partials.image-card', ['image' => $image])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có ảnh nghiệm thu</h5>
                            <p class="text-muted">Tải lên ảnh nghiệm thu sau khi hoàn thành sự kiện</p>
                        </div>
                    @endif
                </div>

                <!-- File thiết kế -->
                <div class="tab-pane fade" id="thiet-ke" role="tabpanel">
                    @if($event->thietKeImages->count() > 0)
                        <div class="row g-3">
                            @foreach($event->thietKeImages->sortByDesc('created_at') as $image)
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    @include('events.images.partials.image-card', ['image' => $image])
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-palette fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có file thiết kế</h5>
                            <p class="text-muted">Tải lên file thiết kế (.AI, .PSD, v.v.)</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem ảnh -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Xem ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Image">
                <div id="modalImageInfo" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <a id="modalDownloadBtn" href="" class="btn btn-primary" download>
                    <i class="fas fa-download me-2"></i>Tải xuống
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.image-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    overflow: hidden;
}

.image-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.image-thumbnail {
    width: 100%;
    height: 200px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.image-thumbnail:hover {
    transform: scale(1.05);
}

.file-icon {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-icon:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.card-body {
    padding: 1rem;
}

.badge-category {
    font-size: 0.75rem;
}

.btn-group-sm .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.nav-tabs .nav-link {
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    font-weight: 600;
}

.upload-progress {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    min-width: 300px;
}
</style>
@endpush

@push('scripts')
<script>
// Xem ảnh trong modal
function viewImage(imagePath, title, info) {
    document.getElementById('modalImage').src = imagePath;
    document.getElementById('imageModalTitle').textContent = title;
    document.getElementById('modalImageInfo').innerHTML = info;
    document.getElementById('modalDownloadBtn').href = imagePath;
    
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Xóa ảnh
function deleteImage(eventId, imageId, imageName) {
    if (confirm(`Bạn có chắc muốn xóa file "${imageName}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/events/${eventId}/images/${imageId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Reset form
function resetForm() {
    document.getElementById('uploadForm').reset();
}

// Xử lý upload với progress bar
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const files = document.getElementById('images').files;
    const imageType = document.getElementById('image_type').value;
    
    if (!files.length) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất một file để tải lên');
        return;
    }
    
    if (!imageType) {
        e.preventDefault();
        alert('Vui lòng chọn loại file');
        return;
    }
    
    // Kiểm tra kích thước file
    let hasLargeFile = false;
    for (let file of files) {
        const maxSize = imageType === 'thiet_ke' ? 50 * 1024 * 1024 : 10 * 1024 * 1024; // 50MB cho thiết kế, 10MB cho ảnh
        if (file.size > maxSize) {
            hasLargeFile = true;
            const maxSizeMB = maxSize / 1024 / 1024;
            alert(`File "${file.name}" có kích thước ${(file.size/1024/1024).toFixed(1)}MB vượt quá giới hạn ${maxSizeMB}MB`);
            break;
        }
    }
    
    if (hasLargeFile) {
        e.preventDefault();
        return;
    }
    
    // Hiển thị loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tải lên...';
    submitBtn.disabled = true;
    
    // Khôi phục button nếu có lỗi
    setTimeout(() => {
        if (submitBtn.disabled) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }, 30000); // 30 giây timeout
});

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bootstrapAlert = new bootstrap.Alert(alert);
            bootstrapAlert.close();
        }, 5000);
    });
});

// Lazy loading cho images
document.addEventListener('DOMContentLoaded', function() {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
});

// Preview files before upload
document.getElementById('images').addEventListener('change', function(e) {
    const files = e.target.files;
    if (files.length > 0) {
        let fileList = '';
        for (let file of files) {
            const size = (file.size / 1024 / 1024).toFixed(1);
            fileList += `• ${file.name} (${size} MB)\n`;
        }
        console.log('Files selected:\n' + fileList);
    }
});
</script>
@endpush
</rewritten_file>