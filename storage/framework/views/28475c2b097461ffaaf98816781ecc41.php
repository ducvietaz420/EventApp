

<?php $__env->startSection('title', 'AI Suggestions'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">AI Suggestions</h1>
                    <p class="text-muted">Gợi ý thông minh từ AI cho sự kiện của bạn</p>
                </div>
                <div>
                    <?php if($event ?? false): ?>
                        <a href="<?php echo e(route('events.ai-suggestions.generate', $event)); ?>" class="btn btn-primary">
                            <i class="fas fa-magic me-2"></i>Tạo gợi ý mới
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
                            <i class="fas fa-magic me-2"></i>Tạo gợi ý mới
                        </button>
                    <?php endif; ?>
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
                            <i class="fas fa-lightbulb fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo e($stats['total'] ?? 0); ?></h5>
                            <small class="text-muted">Tổng gợi ý</small>
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
                            <i class="fas fa-check fa-lg text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo e($stats['accepted'] ?? 0); ?></h5>
                            <small class="text-muted">Đã chấp nhận</small>
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
                            <i class="fas fa-clock fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo e($stats['pending'] ?? 0); ?></h5>
                            <small class="text-muted">Đang chờ</small>
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
                            <i class="fas fa-chart-line fa-lg text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo e($stats['high_confidence'] ?? 0); ?></h5>
                            <small class="text-muted">Độ tin cậy cao</small>
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
                           value="<?php echo e(request('search')); ?>" placeholder="Tìm kiếm gợi ý...">
                </div>
                <?php if(!($event ?? false)): ?>
                <div class="col-md-3">
                    <label for="event_id" class="form-label">Sự kiện</label>
                    <select class="form-select" id="event_id" name="event_id">
                        <option value="">Tất cả sự kiện</option>
                        <?php $__currentLoopData = $events ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($e->id); ?>" <?php echo e(request('event_id') == $e->id ? 'selected' : ''); ?>>
                                <?php echo e($e->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Đang chờ</option>
                        <option value="accepted" <?php echo e(request('status') === 'accepted' ? 'selected' : ''); ?>>Đã chấp nhận</option>
                        <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Đã từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">Danh mục</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Tất cả</option>
                        <option value="budget" <?php echo e(request('category') === 'budget' ? 'selected' : ''); ?>>Ngân sách</option>
                        <option value="timeline" <?php echo e(request('category') === 'timeline' ? 'selected' : ''); ?>>Timeline</option>
                        <option value="venue" <?php echo e(request('category') === 'venue' ? 'selected' : ''); ?>>Địa điểm</option>
                        <option value="catering" <?php echo e(request('category') === 'catering' ? 'selected' : ''); ?>>Ăn uống</option>
                        <option value="decoration" <?php echo e(request('category') === 'decoration' ? 'selected' : ''); ?>>Trang trí</option>
                        <option value="other" <?php echo e(request('category') === 'other' ? 'selected' : ''); ?>>Khác</option>
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

    <!-- AI Suggestions List -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Danh sách gợi ý AI</h6>
        </div>
        <div class="card-body">
            <?php if(($suggestions ?? collect())->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-start border-3 border-<?php echo e($suggestion->status === 'accepted' ? 'success' : ($suggestion->status === 'rejected' ? 'danger' : 'warning')); ?>">
                                <div class="card-header d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-<?php echo e($suggestion->category === 'budget' ? 'primary' : ($suggestion->category === 'timeline' ? 'info' : 'secondary')); ?> me-2">
                                            <?php echo e(match($suggestion->category) {
                                                    'budget' => 'Ngân sách',
                                                    'timeline' => 'Lịch trình',
                                                    'venue' => 'Địa điểm',
                                                    'catering' => 'Ăn uống',
                                                    'decoration' => 'Trang trí',
                                                    'other' => 'Khác',
                                                    default => ucfirst($suggestion->category)
                                                }); ?>

                                        </span>
                                        <span class="badge bg-<?php echo e($suggestion->status === 'accepted' ? 'success' : ($suggestion->status === 'rejected' ? 'danger' : 'warning')); ?>">
                                            <?php echo e($suggestion->status === 'accepted' ? 'Đã chấp nhận' : ($suggestion->status === 'rejected' ? 'Đã từ chối' : 'Đang chờ')); ?>

                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted"><?php echo e($suggestion->confidence ?? 0); ?>% tin cậy</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo e($suggestion->title); ?></h6>
                                    <p class="card-text text-muted small"><?php echo e(Str::limit($suggestion->description ?? '', 100)); ?></p>
                                    
                                    <?php if($suggestion->event): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Sự kiện:</small><br>
                                            <a href="<?php echo e(route('events.show', $suggestion->event)); ?>" class="text-decoration-none">
                                                <?php echo e($suggestion->event->name); ?>

                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i><?php echo e($suggestion->created_at->diffForHumans()); ?>

                                        </small>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if($suggestion->status === 'pending'): ?>
                                                <button type="button" class="btn btn-outline-success" onclick="updateStatus(<?php echo e($suggestion->id); ?>, 'accepted')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="updateStatus(<?php echo e($suggestion->id); ?>, 'rejected')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('ai-suggestions.show', $suggestion)); ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    <?php echo e($suggestions->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Chưa có gợi ý AI nào</h6>
                    <p class="text-muted">Hãy tạo gợi ý đầu tiên cho sự kiện của bạn!</p>
                    <?php if($event ?? false): ?>
                        <a href="<?php echo e(route('events.ai-suggestions.generate', $event)); ?>" class="btn btn-primary">
                            <i class="fas fa-magic me-2"></i>Tạo gợi ý đầu tiên
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
                            <i class="fas fa-magic me-2"></i>Tạo gợi ý đầu tiên
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Generate Modal -->
<?php if(!($event ?? false)): ?>
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo gợi ý AI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_event_id" class="form-label">Sự kiện *</label>
                        <select class="form-select" id="modal_event_id" name="event_id" required>
                            <option value="">Chọn sự kiện...</option>
                            <?php $__currentLoopData = $events ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($e->id); ?>"><?php echo e($e->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_suggestion_type" class="form-label">Loại gợi ý *</label>
                        <select class="form-select" id="modal_suggestion_type" name="suggestion_type" required>
                            <option value="">Chọn loại gợi ý...</option>
                            <option value="budget">Ngân sách</option>
                            <option value="timeline">Lịch trình</option>
                            <option value="checklist">Checklist</option>
                            <option value="supplier">Nhà cung cấp</option>
                            <option value="general">Tổng quát</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_prompt" class="form-label">Yêu cầu chi tiết (tùy chọn)</label>
                        <textarea class="form-control" id="modal_prompt" name="prompt" rows="3" placeholder="Nhập yêu cầu cụ thể cho AI..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="generateBtn">
                        <i class="fas fa-magic me-2"></i>Tạo gợi ý
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function updateStatus(suggestionId, status) {
    if (confirm('Bạn có chắc chắn muốn ' + (status === 'accepted' ? 'chấp nhận' : 'từ chối') + ' gợi ý này?')) {
        fetch(`/ai-suggestions/${suggestionId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái');
        });
    }
}

// Xử lý form tạo gợi ý AI
document.getElementById('generateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const generateBtn = document.getElementById('generateBtn');
    const originalText = generateBtn.innerHTML;
    
    // Disable button và hiển thị loading
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo gợi ý...';
    
    fetch('/ai-suggestions/generate', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hiển thị thông báo thành công
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);
            
            // Đóng modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('generateModal'));
            modal.hide();
            // Reload trang
            setTimeout(() => location.reload(), 1500);
        } else {
            // Hiển thị thông báo lỗi chi tiết
            let errorMessage = data.message;
            if (data.error_type === 'missing_api_key') {
                errorMessage += '\n\nHướng dẫn cấu hình:\n1. Truy cập https://makersuite.google.com/app/apikey\n2. Tạo API key mới\n3. Thêm GEMINI_API_KEY=your_key_here vào file .env\n4. Restart server';
            }
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Lỗi:</strong> ${errorMessage.replace(/\n/g, '<br>')}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Lỗi kết nối:</strong> Không thể kết nối với server. Vui lòng kiểm tra kết nối mạng và thử lại.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);
    })
    .finally(() => {
        // Restore button
        generateBtn.disabled = false;
        generateBtn.innerHTML = originalText;
    });
});
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views/ai-suggestions/index.blade.php ENDPATH**/ ?>