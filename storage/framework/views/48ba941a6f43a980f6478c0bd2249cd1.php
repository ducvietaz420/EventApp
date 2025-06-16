

<?php $__env->startSection('title', 'Báo cáo sự kiện'); ?>

<?php $__env->startSection('content'); ?>
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
                    <?php if($event ?? false): ?>
                        <a href="<?php echo e(route('events.reports.create', $event)); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo mới
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('event-reports.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo mới
                        </a>
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
                            <i class="fas fa-chart-bar fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo e($stats['total'] ?? 0); ?></h5>
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
                            <h5 class="mb-0"><?php echo e($stats['published'] ?? 0); ?></h5>
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
                            <h5 class="mb-0"><?php echo e($stats['drafts'] ?? 0); ?></h5>
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
                            <h5 class="mb-0"><?php echo e($stats['pending_review'] ?? 0); ?></h5>
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
                           value="<?php echo e(request('search')); ?>" placeholder="Tìm kiếm báo cáo...">
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
                    <label for="report_type" class="form-label">Loại báo cáo</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="">Tất cả</option>
                        <option value="financial" <?php echo e(request('report_type') === 'financial' ? 'selected' : ''); ?>>Tài chính</option>
                        <option value="summary" <?php echo e(request('report_type') === 'summary' ? 'selected' : ''); ?>>Tổng quan</option>
                        <option value="attendance" <?php echo e(request('report_type') === 'attendance' ? 'selected' : ''); ?>>Tham dự</option>
                        <option value="feedback" <?php echo e(request('report_type') === 'feedback' ? 'selected' : ''); ?>>Phản hồi</option>
                        <option value="final" <?php echo e(request('report_type') === 'final' ? 'selected' : ''); ?>>Tổng kết</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>Bản nháp</option>
                        <option value="under_review" <?php echo e(request('status') === 'under_review' ? 'selected' : ''); ?>>Chờ duyệt</option>
                        <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Đã duyệt</option>
                        <option value="published" <?php echo e(request('status') === 'published' ? 'selected' : ''); ?>>Đã xuất bản</option>
                        <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Đã từ chối</option>
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
            <?php if(($reports ?? collect())->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-start border-3 border-<?php echo e($report->status === 'published' ? 'success' : ($report->status === 'draft' ? 'warning' : 'info')); ?>">
                                <div class="card-header d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-<?php echo e($report->report_type === 'financial' ? 'primary' : ($report->report_type === 'final' ? 'success' : 'secondary')); ?> me-2">
                                            <?php echo e(ucfirst($report->report_type)); ?>

                                        </span>
                                        <span class="badge bg-<?php echo e($report->status === 'published' ? 'success' : ($report->status === 'draft' ? 'warning' : 'info')); ?>">
                                            <?php echo e($report->status === 'published' ? 'Đã xuất bản' : ($report->status === 'draft' ? 'Bản nháp' : 'Chờ duyệt')); ?>

                                        </span>
                                    </div>
                                    <?php if($report->success_score ?? false): ?>
                                        <div class="text-end">
                                            <small class="text-muted"><?php echo e(number_format($report->success_score, 1)); ?>% thành công</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="<?php echo e(route('event-reports.show', $report)); ?>" class="text-decoration-none">
                                            <?php echo e($report->title); ?>

                                        </a>
                                    </h6>
                                    <?php if($report->summary): ?>
                                        <p class="card-text text-muted small"><?php echo e(Str::limit($report->summary, 100)); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if($report->event): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Sự kiện:</small><br>
                                            <a href="<?php echo e(route('events.show', $report->event)); ?>" class="text-decoration-none">
                                                <?php echo e($report->event->name); ?>

                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($report->roi_percentage ?? false): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">ROI:</small>
                                            <span class="badge bg-<?php echo e($report->roi_percentage > 0 ? 'success' : 'danger'); ?>">
                                                <?php echo e(number_format($report->roi_percentage, 1)); ?>%
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?php echo e($report->created_at->format('d/m/Y')); ?>

                                        </small>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if($report->status === 'draft'): ?>
                                                <a href="<?php echo e(route('event-reports.edit', $report)); ?>" class="btn btn-outline-warning" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo e(route('event-reports.show', $report)); ?>" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if($report->status === 'published'): ?>
                                                <a href="<?php echo e(route('event-reports.exportPdf', $report)); ?>" class="btn btn-outline-success" title="Xuất PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <!-- Pagination -->
                <?php if(method_exists($reports, 'links')): ?>
                    <div class="d-flex justify-content-center">
                        <?php echo e($reports->links()); ?>

                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Chưa có báo cáo nào</h6>
                    <p class="text-muted">Hãy tạo báo cáo đầu tiên cho sự kiện của bạn!</p>
                    <?php if($event ?? false): ?>
                        <a href="<?php echo e(route('events.reports.create', $event)); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo đầu tiên
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('event-reports.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo báo cáo đầu tiên
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\event-reports\index.blade.php ENDPATH**/ ?>