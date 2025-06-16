<?php $__env->startSection('title', 'Chi tiết công việc'); ?>
<?php $__env->startSection('page-title', 'Chi tiết công việc: ' . $checklist->title); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="btn-group" role="group">
        <a href="<?php echo e(route('checklists.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
        <a href="<?php echo e(route('checklists.edit', $checklist)); ?>" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-2"></i>Hành động
            </button>
            <ul class="dropdown-menu">
                <li>
                    <?php if(!$checklist->is_completed): ?>
                        <a class="dropdown-item" href="#" onclick="toggleComplete(<?php echo e($checklist->id); ?>, true)">
                            <i class="fas fa-check-circle text-success me-2"></i>Đánh dấu hoàn thành
                        </a>
                    <?php else: ?>
                        <a class="dropdown-item" href="#" onclick="toggleComplete(<?php echo e($checklist->id); ?>, false)">
                            <i class="fas fa-undo text-warning me-2"></i>Đánh dấu chưa hoàn thành
                        </a>
                    <?php endif; ?>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="#" onclick="duplicateChecklist(<?php echo e($checklist->id); ?>)">
                        <i class="fas fa-copy text-info me-2"></i>Nhân bản công việc
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?php echo e(route('events.show', $checklist->event)); ?>">
                        <i class="fas fa-calendar text-primary me-2"></i>Xem sự kiện
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="exportChecklist()">
                        <i class="fas fa-download text-secondary me-2"></i>Xuất báo cáo
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Xóa công việc
                    </a>
                </li>
            </ul>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <!-- Thông tin chính -->
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Thông tin công việc</h6>
                <div>
                    <?php if($checklist->is_important): ?>
                        <span class="badge bg-warning me-2">
                            <i class="fas fa-star"></i> Quan trọng
                        </span>
                    <?php endif; ?>
                    <span class="badge bg-<?php echo e($checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                        <?php echo e(ucfirst($checklist->priority)); ?>

                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3"><?php echo e($checklist->title); ?></h5>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sự kiện:</label>
                            <div>
                                <a href="<?php echo e(route('events.show', $checklist->event)); ?>" class="text-decoration-none">
                                    <i class="fas fa-calendar text-primary me-2"></i><?php echo e($checklist->event->name); ?>

                                </a>
                                <span class="badge bg-light text-dark ms-2"><?php echo e(ucfirst($checklist->event->type)); ?></span>
                            </div>
                        </div>
                        
                        <?php if($checklist->description): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả:</label>
                                <p class="mb-0"><?php echo e($checklist->description); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($checklist->due_date): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hạn thực hiện:</label>
                                <div>
                                    <i class="fas fa-clock text-muted me-2"></i>
                                    <?php echo e($checklist->due_date->format('d/m/Y')); ?>

                                    <?php if($checklist->due_time): ?>
                                        lúc <?php echo e($checklist->due_time->format('H:i')); ?>

                                    <?php endif; ?>
                                    
                                    <?php
                                        $now = now();
                                        $dueDateTime = $checklist->due_date;
                                        if ($checklist->due_time) {
                                            $dueDateTime = $checklist->due_date->setTimeFromTimeString($checklist->due_time->format('H:i:s'));
                                        }
                                        $isOverdue = $dueDateTime < $now && !$checklist->is_completed;
                                        $daysLeft = $now->diffInDays($dueDateTime, false);
                                    ?>
                                    
                                    <?php if($isOverdue): ?>
                                        <span class="badge bg-danger ms-2">
                                            <i class="fas fa-exclamation-triangle"></i> Quá hạn <?php echo e(abs($daysLeft)); ?> ngày
                                        </span>
                                    <?php elseif($daysLeft <= 1 && !$checklist->is_completed): ?>
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-clock"></i> Sắp hết hạn
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái:</label>
                            <div>
                                <?php if($checklist->is_completed): ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check-circle"></i> Hoàn thành
                                    </span>
                                    <?php if($checklist->completed_at): ?>
                                        <small class="text-muted ms-2">
                                            (<?php echo e($checklist->completed_at->format('d/m/Y H:i')); ?>)
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-clock"></i> Đang chờ
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Độ ưu tiên:</label>
                            <div>
                                <span class="badge bg-<?php echo e($checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary')); ?> fs-6">
                                    <?php if($checklist->priority === 'high'): ?>
                                        <i class="fas fa-arrow-up"></i> Cao
                                    <?php elseif($checklist->priority === 'medium'): ?>
                                        <i class="fas fa-minus"></i> Trung bình
                                    <?php else: ?>
                                        <i class="fas fa-arrow-down"></i> Thấp
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <?php if($checklist->assigned_to): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Người phụ trách:</label>
                                <div>
                                    <i class="fas fa-user text-muted me-2"></i><?php echo e($checklist->assigned_to); ?>

                                    <?php if($checklist->contact_info): ?>
                                        <br><small class="text-muted"><?php echo e($checklist->contact_info); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($checklist->estimated_duration): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian ước tính:</label>
                                <div>
                                    <i class="fas fa-hourglass text-muted me-2"></i><?php echo e($checklist->estimated_duration); ?> phút
                                    <small class="text-muted">(<?php echo e(number_format($checklist->estimated_duration / 60, 1)); ?> giờ)</small>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($checklist->reminder_before): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nhắc nhở trước:</label>
                                <div>
                                    <i class="fas fa-bell text-muted me-2"></i>
                                    <?php if($checklist->reminder_before >= 1440): ?>
                                        <?php echo e($checklist->reminder_before / 1440); ?> ngày
                                    <?php elseif($checklist->reminder_before >= 60): ?>
                                        <?php echo e($checklist->reminder_before / 60); ?> giờ
                                    <?php else: ?>
                                        <?php echo e($checklist->reminder_before); ?> phút
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($checklist->notes): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ghi chú:</label>
                                <div class="border rounded p-2 bg-light">
                                    <?php echo e($checklist->notes); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Công việc liên quan -->
        <?php if($relatedChecklists->count() > 0): ?>
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="mb-0">Công việc liên quan trong sự kiện</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php $__currentLoopData = $relatedChecklists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border-start border-3 border-<?php echo e($related->priority === 'high' ? 'danger' : ($related->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="<?php echo e(route('checklists.show', $related)); ?>" class="text-decoration-none">
                                                        <?php echo e($related->title); ?>

                                                    </a>
                                                    <?php if($related->is_important): ?>
                                                        <i class="fas fa-star text-warning ms-1"></i>
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?php if($related->due_date): ?>
                                                        <i class="fas fa-clock me-1"></i><?php echo e($related->due_date->format('d/m')); ?>

                                                    <?php endif; ?>
                                                    <?php if($related->assigned_to): ?>
                                                        <i class="fas fa-user ms-2 me-1"></i><?php echo e($related->assigned_to); ?>

                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <?php if($related->is_completed): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <!-- Thống kê nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Thống kê nhanh</h6>
            </div>
            <div class="card-body">
                <?php
                    $progress = 0;
                    if ($checklist->is_completed) {
                        $progress = 100;
                    } elseif ($checklist->due_date) {
                        $totalDays = $checklist->created_at->diffInDays($checklist->due_date);
                        $passedDays = $checklist->created_at->diffInDays(now());
                        $progress = $totalDays > 0 ? min(100, ($passedDays / $totalDays) * 100) : 0;
                    }
                ?>
                
                <div class="text-center mb-3">
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-<?php echo e($checklist->is_completed ? 'success' : ($progress > 80 ? 'danger' : ($progress > 50 ? 'warning' : 'info'))); ?>" 
                             style="width: <?php echo e($progress); ?>%"></div>
                    </div>
                    <small class="text-muted">Tiến độ thời gian: <?php echo e(number_format($progress, 1)); ?>%</small>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <?php if($checklist->due_date): ?>
                                <?php
                                    $now = now();
                                    $dueDateTime = $checklist->due_date;
                                    if ($checklist->due_time) {
                                        $dueDateTime = $checklist->due_date->setTimeFromTimeString($checklist->due_time->format('H:i:s'));
                                    }
                                    $daysLeft = $now->diffInDays($dueDateTime, false);
                                ?>
                                
                                <h5 class="mb-0 text-<?php echo e($daysLeft < 0 ? 'danger' : ($daysLeft <= 1 ? 'warning' : 'info')); ?>">
                                    <?php echo e(abs($daysLeft)); ?>

                                </h5>
                                <small class="text-muted">
                                    <?php echo e($daysLeft < 0 ? 'Ngày quá hạn' : ($daysLeft == 0 ? 'Hôm nay' : 'Ngày còn lại')); ?>

                                </small>
                            <?php else: ?>
                                <h5 class="mb-0 text-muted">--</h5>
                                <small class="text-muted">Không đặt hạn</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-<?php echo e($checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                            <?php echo e(ucfirst($checklist->priority)); ?>

                        </h5>
                        <small class="text-muted">Độ ưu tiên</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hành động nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Hành động nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if(!$checklist->is_completed): ?>
                        <button type="button" class="btn btn-success" onclick="toggleComplete(<?php echo e($checklist->id); ?>, true)">
                            <i class="fas fa-check-circle me-2"></i>Đánh dấu hoàn thành
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-warning" onclick="toggleComplete(<?php echo e($checklist->id); ?>, false)">
                            <i class="fas fa-undo me-2"></i>Đánh dấu chưa hoàn thành
                        </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('checklists.edit', $checklist)); ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa công việc
                    </a>
                    
                    <button type="button" class="btn btn-info" onclick="duplicateChecklist(<?php echo e($checklist->id); ?>)">
                        <i class="fas fa-copy me-2"></i>Nhân bản công việc
                    </button>
                    
                    <a href="<?php echo e(route('events.show', $checklist->event)); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-2"></i>Xem sự kiện
                    </a>
                    
                    <button type="button" class="btn btn-outline-secondary" onclick="exportChecklist()">
                        <i class="fas fa-download me-2"></i>Xuất báo cáo
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Thông tin hệ thống -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Thông tin hệ thống</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-plus text-muted me-1"></i>Tạo lúc:</span>
                        <span><?php echo e($checklist->created_at->format('d/m/Y H:i')); ?></span>
                    </div>
                    
                    <?php if($checklist->updated_at != $checklist->created_at): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-edit text-muted me-1"></i>Cập nhật cuối:</span>
                            <span><?php echo e($checklist->updated_at->format('d/m/Y H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($checklist->is_completed && $checklist->completed_at): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-check-circle text-success me-1"></i>Hoàn thành lúc:</span>
                            <span><?php echo e($checklist->completed_at->format('d/m/Y H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-hashtag text-muted me-1"></i>ID:</span>
                        <span><?php echo e($checklist->id); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-bell text-muted me-1"></i>Thông báo:</span>
                        <span>
                            <?php if($checklist->send_notification): ?>
                                <span class="badge bg-success">Bật</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Tắt</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
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
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h5>Bạn có chắc chắn muốn xóa công việc này?</h5>
                    <p class="text-muted">Hành động này không thể hoàn tác!</p>
                    
                    <div class="alert alert-warning text-start mt-3">
                        <strong>Thông tin công việc sẽ bị xóa:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Tên: <?php echo e($checklist->title); ?></li>
                            <li>Sự kiện: <?php echo e($checklist->event->name); ?></li>
                            <?php if($checklist->due_date): ?>
                                <li>Hạn: <?php echo e($checklist->due_date->format('d/m/Y')); ?></li>
                            <?php endif; ?>
                            <?php if($checklist->assigned_to): ?>
                                <li>Người phụ trách: <?php echo e($checklist->assigned_to); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <form method="POST" action="<?php echo e(route('checklists.destroy', $checklist)); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Xóa công việc
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Toggle complete status
function toggleComplete(checklistId, isCompleted) {
    const action = isCompleted ? 'complete' : 'incomplete';
    const message = isCompleted ? 'Đánh dấu công việc đã hoàn thành?' : 'Đánh dấu công việc chưa hoàn thành?';
    
    if (confirm(message)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}/${action}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Duplicate checklist
function duplicateChecklist(checklistId) {
    if (confirm('Bạn có muốn nhân bản công việc này?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}/duplicate`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Export checklist report
function exportChecklist() {
    const checklist = {
        title: '<?php echo e($checklist->title); ?>',
        event: '<?php echo e($checklist->event->name); ?>',
        description: '<?php echo e($checklist->description); ?>',
        priority: '<?php echo e($checklist->priority); ?>',
        status: '<?php echo e($checklist->is_completed ? "Hoàn thành" : "Đang chờ"); ?>',
        assigned_to: '<?php echo e($checklist->assigned_to); ?>',
        due_date: '<?php echo e($checklist->due_date ? $checklist->due_date->format("d/m/Y") : ""); ?>',
        due_time: '<?php echo e($checklist->due_time ? $checklist->due_time->format("H:i") : ""); ?>',
        estimated_duration: '<?php echo e($checklist->estimated_duration); ?>',
        reminder_before: '<?php echo e($checklist->reminder_before); ?>',
        notes: '<?php echo e($checklist->notes); ?>',
        created_at: '<?php echo e($checklist->created_at->format("d/m/Y H:i")); ?>',
        updated_at: '<?php echo e($checklist->updated_at->format("d/m/Y H:i")); ?>'
    };
    
    let content = `BÁO CÁO CÔNG VIỆC\n`;
    content += `===================\n\n`;
    content += `Tên công việc: ${checklist.title}\n`;
    content += `Sự kiện: ${checklist.event}\n`;
    
    if (checklist.description) {
        content += `Mô tả: ${checklist.description}\n`;
    }
    
    content += `Độ ưu tiên: ${checklist.priority === 'high' ? 'Cao' : checklist.priority === 'medium' ? 'Trung bình' : 'Thấp'}\n`;
    content += `Trạng thái: ${checklist.status}\n`;
    
    if (checklist.assigned_to) {
        content += `Người phụ trách: ${checklist.assigned_to}\n`;
    }
    
    if (checklist.due_date) {
        content += `Hạn thực hiện: ${checklist.due_date}`;
        if (checklist.due_time) {
            content += ` lúc ${checklist.due_time}`;
        }
        content += `\n`;
    }
    
    if (checklist.estimated_duration) {
        content += `Thời gian ước tính: ${checklist.estimated_duration} phút\n`;
    }
    
    if (checklist.reminder_before) {
        content += `Nhắc nhở trước: ${checklist.reminder_before} phút\n`;
    }
    
    if (checklist.notes) {
        content += `Ghi chú: ${checklist.notes}\n`;
    }
    
    content += `\n--- THÔNG TIN HỆ THỐNG ---\n`;
    content += `Tạo lúc: ${checklist.created_at}\n`;
    content += `Cập nhật cuối: ${checklist.updated_at}\n`;
    content += `\n--- HẾT BÁO CÁO ---`;
    
    // Create and download file
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `bao-cao-cong-viec-${checklist.title.toLowerCase().replace(/\s+/g, '-')}-${new Date().getTime()}.txt`;
    link.click();
    
    // Show success message
    showToast('Đã xuất báo cáo công việc thành công!', 'success');
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\checklists\show.blade.php ENDPATH**/ ?>