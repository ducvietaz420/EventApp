<?php $__env->startSection('title', $event->name . ' - Chi tiết sự kiện'); ?>
<?php $__env->startSection('page-title', $event->name); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="btn-group" role="group">
        <a href="<?php echo e(route('events.images.index', $event)); ?>" class="btn btn-info">
            <i class="fas fa-images me-2"></i>Quản lý ảnh
        </a>
        <a href="<?php echo e(route('events.edit', $event->id)); ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa
        </a>
        <button type="button" class="btn btn-danger" onclick="deleteEvent(<?php echo e($event->id); ?>, '<?php echo e($event->name); ?>')">
            <i class="fas fa-trash me-2"></i>Xóa
        </button>
        <a href="<?php echo e(route('events.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
                                <td><span class="badge bg-info"><?php echo e($event->type_display); ?></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trạng thái:</td>
                                <td>
                                    <span class="status-badge status-<?php echo e($event->status); ?>">
                                        <?php switch($event->status):
                                            case ('planning'): ?>
                                                Đang lên kế hoạch
                                                <?php break; ?>
                                            <?php case ('in_progress'): ?>
                                                Đang tiến hành
                                                <?php break; ?>
                                            <?php case ('completed'): ?>
                                                Hoàn thành
                                                <?php break; ?>
                                            <?php case ('cancelled'): ?>
                                                Đã hủy
                                                <?php break; ?>
                                            <?php default: ?>
                                                <?php echo e(ucfirst($event->status)); ?>

                                        <?php endswitch; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày diễn ra:</td>
                                <td>
                                    <?php if($event->event_date): ?>
                                        <strong><?php echo e($event->event_date->format('d/m/Y')); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa xác định</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Địa điểm:</td>
                                <td>
                                    <?php if($event->venue): ?>
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        <?php echo e($event->venue); ?>

                                    <?php else: ?>
                                        <span class="text-muted">Chưa xác định</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Số khách mời:</td>
                                <td>
                                    <?php if($event->expected_guests): ?>
                                        <i class="fas fa-users text-muted me-1"></i>
                                        <?php echo e(number_format($event->expected_guests)); ?> người
                                    <?php else: ?>
                                        <span class="text-muted">Chưa xác định</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Mô tả</h6>
                        <p class="text-muted">
                            <?php echo e($event->description ?: 'Chưa có mô tả'); ?>

                        </p>
                        
                        <?php if($event->notes): ?>
                            <h6 class="text-muted mb-2 mt-3">Ghi chú</h6>
                            <p class="text-muted"><?php echo e($event->notes); ?></p>
                        <?php endif; ?>
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
                <?php
                    $totalTasks = $event->checklists->count();
                    $completedTasks = $event->checklists->whereNotNull('completed_at')->count();
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                ?>
                <div class="progress mb-3" style="height: 15px;">
                    <div class="progress-bar" role="progressbar" 
                         style="width: <?php echo e($progress); ?>%" 
                         aria-valuenow="<?php echo e($progress); ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?php echo e($progress); ?>%
                    </div>
                </div>
                <p class="mb-0"><?php echo e($completedTasks); ?>/<?php echo e($totalTasks); ?> nhiệm vụ hoàn thành</p>
            </div>
        </div>
        
        <!-- Ảnh sự kiện -->
        <div class="card shadow">
            <div class="card-body text-center">
                <h6 class="text-muted mb-3">Ảnh sự kiện</h6>
                <div class="row text-center">
                    <div class="col-12 mb-2">
                        <h5 class="text-primary mb-0"><?php echo e($event->total_images); ?></h5>
                        <small class="text-muted">Tổng số ảnh</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-success mb-0"><?php echo e($event->total_nghiem_thu_images); ?></h6>
                        <small class="text-muted">Nghiệm thu</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-info mb-0"><?php echo e($event->total_thiet_ke_images); ?></h6>
                        <small class="text-muted">Thiết kế</small>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo e(route('events.images.index', $event)); ?>" class="btn btn-sm btn-outline-primary">
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
                    <a href="<?php echo e(route('events.checklists.create', $event->id)); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Thêm công việc
                    </a>
                </div>
                
                <?php if($event->checklists->count() > 0): ?>
                    <?php
                        $groupedChecklists = $event->checklists->groupBy('category');
                    ?>
                    
                    <?php $__currentLoopData = $groupedChecklists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $checklists): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-folder me-2"></i><?php echo e(match($category) {
                                        'venue' => 'Địa điểm',
                                        'catering' => 'Catering',
                                        'decoration' => 'Trang trí',
                                        'equipment' => 'Thiết bị',
                                        'marketing' => 'Marketing',
                                        'staff' => 'Nhân sự',
                                        'transportation' => 'Vận chuyển',
                                        'other' => 'Khác',
                                        default => ucfirst($category)
                                    }); ?>

                            </h6>
                            
                            <?php $__currentLoopData = $checklists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $checklist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check me-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       <?php echo e($checklist->completed_at ? 'checked' : ''); ?>

                                                       onchange="toggleComplete(<?php echo e($checklist->id); ?>)">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 <?php echo e($checklist->completed_at ? 'text-decoration-line-through text-muted' : ''); ?>">
                                                    <?php echo e($checklist->title); ?>

                                                </h6>
                                                <?php if($checklist->description): ?>
                                                    <p class="text-muted mb-1 small"><?php echo e($checklist->description); ?></p>
                                                <?php endif; ?>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-<?php echo e($checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                                        <?php echo e(ucfirst($checklist->priority)); ?>

                                                    </span>
                                                    <?php if($checklist->due_date): ?>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?php echo e($checklist->due_date->format('d/m/Y')); ?>

                                                        </small>
                                                    <?php endif; ?>
                                                    <?php if($checklist->completed_at): ?>
                                                        <small class="text-success">
                                                            <i class="fas fa-check me-1"></i>
                                                            Hoàn thành <?php echo e($checklist->completed_at->format('d/m/Y')); ?>

                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-square fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có công việc nào</p>
                        <a href="<?php echo e(route('events.checklists.create', $event->id)); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo công việc đầu tiên
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Images Tab -->
            <div class="tab-pane fade" id="images" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Ảnh sự kiện</h6>
                    <a href="<?php echo e(route('events.images.index', $event)); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-images me-1"></i>Quản lý ảnh
                    </a>
                </div>
                
                <?php if($event->images->count() > 0): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Ảnh Nghiệm Thu</h6>
                                </div>
                                <div class="card-body">
                                    <?php if($event->nghiemThuImages->count() > 0): ?>
                                        <div class="row g-2">
                                            <?php $__currentLoopData = $event->nghiemThuImages->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-4">
                                                    <img src="<?php echo e($image->file_url); ?>" 
                                                         alt="<?php echo e($image->original_filename); ?>" 
                                                         class="img-fluid rounded"
                                                         style="width: 100%; height: 80px; object-fit: cover;">
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <?php if($event->nghiemThuImages->count() > 6): ?>
                                            <div class="text-center mt-2">
                                                <small class="text-muted">và <?php echo e($event->nghiemThuImages->count() - 6); ?> ảnh khác</small>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-center py-3">
                                            <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có ảnh nghiệm thu</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-palette me-2"></i>Ảnh Thiết Kế</h6>
                                </div>
                                <div class="card-body">
                                    <?php if($event->thietKeImages->count() > 0): ?>
                                        <div class="row g-2">
                                            <?php $__currentLoopData = $event->thietKeImages->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-4">
                                                    <img src="<?php echo e($image->file_url); ?>" 
                                                         alt="<?php echo e($image->original_filename); ?>" 
                                                         class="img-fluid rounded"
                                                         style="width: 100%; height: 80px; object-fit: cover;">
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <?php if($event->thietKeImages->count() > 6): ?>
                                            <div class="text-center mt-2">
                                                <small class="text-muted">và <?php echo e($event->thietKeImages->count() - 6); ?> ảnh khác</small>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-center py-3">
                                            <i class="fas fa-images fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có ảnh thiết kế</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo e(route('events.images.index', $event)); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Xem tất cả ảnh (<?php echo e($event->total_images); ?>)
                        </a>
                        <?php if($event->images->count() > 0): ?>
                            <a href="<?php echo e(route('events.images.download-zip', $event)); ?>" class="btn btn-outline-success">
                                <i class="fas fa-download me-1"></i>Tải ZIP
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có ảnh nào</p>
                        <a href="<?php echo e(route('events.images.index', $event)); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Thêm ảnh đầu tiên
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- AI Suggestions Tab -->
            <div class="tab-pane fade" id="ai-suggestions" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">AI Suggestions</h6>
                    <form action="<?php echo e(route('events.ai-suggestions.generate', $event->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-robot me-1"></i>Tạo gợi ý mới
                        </button>
                    </form>
                </div>
                
                <?php if($event->aiSuggestions->count() > 0): ?>
                    <?php $__currentLoopData = $event->aiSuggestions->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1"><?php echo e($suggestion->suggestion_type); ?></h6>
                                        <span class="badge bg-<?php echo e($suggestion->status === 'accepted' ? 'success' : ($suggestion->status === 'rejected' ? 'danger' : 'warning')); ?>">
                                            <?php echo e(ucfirst($suggestion->status)); ?>

                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted"><?php echo e($suggestion->created_at->format('d/m/Y H:i')); ?></small>
                                    </div>
                                </div>
                                <p class="mb-2"><?php echo e($suggestion->content); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Độ tin cậy: <?php echo e($suggestion->confidence_score); ?>% | 
                                        Model: <?php echo e($suggestion->ai_model); ?>

                                    </small>
                                    <?php if($suggestion->status === 'pending'): ?>
                                        <div class="btn-group btn-group-sm">
                                            <form action="<?php echo e(route('events.ai-suggestions.accept', [$event->id, $suggestion->id])); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('events.ai-suggestions.reject', [$event->id, $suggestion->id])); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có gợi ý AI nào</p>
                        <form action="<?php echo e(route('events.ai-suggestions.generate', $event->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-robot me-2"></i>Tạo gợi ý đầu tiên
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
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
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteEvent(eventId, eventName) {
    document.getElementById('eventName').textContent = eventName;
    document.getElementById('deleteForm').action = `/events/${eventId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleComplete(checklistId) {
    fetch(`/events/<?php echo e($event->id); ?>/checklists/${checklistId}/complete`, {
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views/events/show.blade.php ENDPATH**/ ?>