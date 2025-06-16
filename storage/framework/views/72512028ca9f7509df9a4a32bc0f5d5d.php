<?php $__env->startSection('title', 'Chi tiết mốc thời gian'); ?>
<?php $__env->startSection('page-title', $timeline->title); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="btn-group" role="group">
        <a href="<?php echo e(route('timelines.edit', $timeline)); ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa
        </a>
        <a href="<?php echo e(route('timelines.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-2"></i>Hành động
            </button>
            <ul class="dropdown-menu">
                <?php if($timeline->status !== 'completed'): ?>
                    <li><a class="dropdown-item" href="#" onclick="markAsCompleted(<?php echo e($timeline->id); ?>)">
                        <i class="fas fa-check text-success me-2"></i>Đánh dấu hoàn thành
                    </a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="#" onclick="markAsPending(<?php echo e($timeline->id); ?>)">
                        <i class="fas fa-undo text-warning me-2"></i>Đánh dấu chưa hoàn thành
                    </a></li>
                <?php endif; ?>
                <li><a class="dropdown-item" href="#" onclick="duplicateTimeline(<?php echo e($timeline->id); ?>)">
                    <i class="fas fa-copy text-info me-2"></i>Nhân bản
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="exportTimeline()">
                    <i class="fas fa-download text-primary me-2"></i>Xuất báo cáo
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-2"></i>Xóa mốc thời gian
                </a></li>
            </ul>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8">
        <!-- Thông tin chính -->
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thông tin mốc thời gian</h5>
                <div>
                    <?php if($timeline->is_milestone): ?>
                        <span class="badge bg-info me-2">Mốc quan trọng</span>
                    <?php endif; ?>
                    <span class="badge bg-<?php echo e($timeline->status === 'completed' ? 'success' : ($timeline->end_time && $timeline->end_time->isPast() && $timeline->status !== 'completed' ? 'danger' : ($timeline->status === 'in_progress' ? 'primary' : 'warning'))); ?>">
                        <?php if($timeline->status === 'completed'): ?>
                            <i class="fas fa-check-circle me-1"></i>Hoàn thành
                        <?php elseif($timeline->end_time && $timeline->end_time->isPast() && $timeline->status !== 'completed'): ?>
                            <i class="fas fa-exclamation-triangle me-1"></i>Quá hạn
                        <?php elseif($timeline->status === 'in_progress'): ?>
                            <i class="fas fa-play-circle me-1"></i>Đang thực hiện
                        <?php else: ?>
                            <i class="fas fa-clock me-1"></i>Đang chờ
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Thông tin cơ bản</h6>
                        
                        <div class="mb-3">
                            <strong>Sự kiện:</strong><br>
                            <a href="<?php echo e(route('events.show', $timeline->event)); ?>" class="text-decoration-none">
                                <?php echo e($timeline->event->name); ?>

                            </a>
                            <span class="badge bg-secondary ms-2"><?php echo e(ucfirst($timeline->event->type)); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Tiêu đề:</strong><br>
                            <?php echo e($timeline->title); ?>

                        </div>
                        
                        <?php if($timeline->description): ?>
                            <div class="mb-3">
                                <strong>Mô tả:</strong><br>
                                <div class="text-muted"><?php echo e($timeline->description); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <strong>Độ ưu tiên:</strong><br>
                            <span class="badge bg-<?php echo e($timeline->priority === 'high' ? 'danger' : ($timeline->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                <?php switch($timeline->priority):
                                    case ('high'): ?>
                                        <i class="fas fa-flag me-1"></i>Cao
                                        <?php break; ?>
                                    <?php case ('medium'): ?>
                                        <i class="fas fa-flag me-1"></i>Trung bình
                                        <?php break; ?>
                                    <?php default: ?>
                                        <i class="fas fa-flag me-1"></i>Thấp
                                <?php endswitch; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Thời gian & Địa điểm</h6>
                        
                        <?php if($timeline->start_time): ?>
                            <div class="mb-3">
                                <strong>Thời gian bắt đầu:</strong><br>
                                <?php echo e($timeline->start_time->format('d/m/Y H:i')); ?>

                                
                                <?php if($timeline->start_time->isToday()): ?>
                                    <span class="badge bg-warning ms-1">Hôm nay</span>
                                <?php elseif($timeline->start_time->isTomorrow()): ?>
                                    <span class="badge bg-info ms-1">Ngày mai</span>
                                <?php elseif($timeline->start_time->isPast() && $timeline->status !== 'completed'): ?>
                                    <span class="badge bg-danger ms-1">Đã qua <?php echo e($timeline->start_time->diffForHumans()); ?></span>
                                <?php elseif(!$timeline->start_time->isPast()): ?>
                                    <span class="badge bg-success ms-1"><?php echo e($timeline->start_time->diffForHumans()); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($timeline->end_time): ?>
                            <div class="mb-3">
                                <strong>Thời gian kết thúc:</strong><br>
                                <?php echo e($timeline->end_time->format('d/m/Y H:i')); ?>

                            </div>
                        <?php endif; ?>
                        
                        <?php if($timeline->estimated_duration): ?>
                            <div class="mb-3">
                                <strong>Thời lượng dự kiến:</strong><br>
                                <?php echo e($timeline->estimated_duration); ?> phút
                                <?php if($timeline->estimated_duration >= 60): ?>
                                    (<?php echo e(floor($timeline->estimated_duration / 60)); ?> giờ <?php echo e($timeline->estimated_duration % 60); ?> phút)
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($timeline->location): ?>
                            <div class="mb-3">
                                <strong>Địa điểm:</strong><br>
                                <i class="fas fa-map-marker-alt text-muted me-1"></i><?php echo e($timeline->location); ?>

                            </div>
                        <?php endif; ?>
                        
                        <?php if($timeline->reminder_before): ?>
                            <div class="mb-3">
                                <strong>Nhắc nhở trước:</strong><br>
                                <i class="fas fa-bell text-muted me-1"></i>
                                <?php if($timeline->reminder_before >= 1440): ?>
                                    <?php echo e($timeline->reminder_before / 1440); ?> ngày
                                <?php elseif($timeline->reminder_before >= 60): ?>
                                    <?php echo e($timeline->reminder_before / 60); ?> giờ
                                <?php else: ?>
                                    <?php echo e($timeline->reminder_before); ?> phút
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if($timeline->responsible_person): ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted mb-2">Người phụ trách</h6>
                            
                            <?php if($timeline->responsible_person): ?>
                                <div class="mb-2">
                                    <strong>Tên:</strong> <?php echo e($timeline->responsible_person); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($timeline->notes): ?>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-muted mb-2">Ghi chú</h6>
                            <div class="bg-light p-3 rounded">
                                <?php echo e($timeline->notes); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Timeline liên quan -->
        <?php if($relatedTimelines->count() > 0): ?>
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Mốc thời gian liên quan trong sự kiện</h5>
                </div>
                <div class="card-body">
                    <div class="timeline-related">
                        <?php $__currentLoopData = $relatedTimelines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="timeline-item <?php echo e($related->id === $timeline->id ? 'current' : ''); ?> <?php echo e($related->is_completed ? 'completed' : ''); ?>">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php if($related->id === $timeline->id): ?>
                                                    <strong><?php echo e($related->title); ?></strong>
                                                    <span class="badge bg-primary ms-2">Hiện tại</span>
                                                <?php else: ?>
                                                    <a href="<?php echo e(route('timelines.show', $related)); ?>" class="text-decoration-none">
                                                        <?php echo e($related->title); ?>

                                                    </a>
                                                <?php endif; ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php if($related->due_date): ?>
                                                    <?php echo e($related->due_date->format('d/m/Y')); ?>

                                                    <?php if($related->due_time): ?>
                                                        <?php echo e($related->due_time->format('H:i')); ?>

                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if($related->location): ?>
                                                    - <?php echo e($related->location); ?>

                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?php echo e($related->priority === 'high' ? 'danger' : ($related->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                                <?php echo e(ucfirst($related->priority)); ?>

                                            </span>
                                            <?php if($related->is_completed): ?>
                                                <span class="badge bg-success ms-1">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($related->description): ?>
                                        <p class="mb-0 mt-2 text-muted small"><?php echo e(Str::limit($related->description, 100)); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <!-- Thống kê nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Thống kê nhanh</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="mb-0 text-<?php echo e($timeline->is_completed ? 'success' : 'warning'); ?>">
                                <?php if($timeline->is_completed): ?>
                                    100%
                                <?php else: ?>
                                    0%
                                <?php endif; ?>
                            </h4>
                            <small class="text-muted">Hoàn thành</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="mb-0 text-info">
                            <?php if($timeline->due_date): ?>
                                <?php if($timeline->due_date->isPast()): ?>
                                    <?php echo e(abs($timeline->due_date->diffInDays())); ?>

                                <?php else: ?>
                                    <?php echo e($timeline->due_date->diffInDays()); ?>

                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </h4>
                        <small class="text-muted">
                            <?php if($timeline->due_date): ?>
                                <?php if($timeline->due_date->isPast()): ?>
                                    Ngày quá hạn
                                <?php else: ?>
                                    Ngày còn lại
                                <?php endif; ?>
                            <?php else: ?>
                                Chưa xác định
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                
                <?php if($timeline->due_date): ?>
                    <div class="mt-3">
                        <div class="progress" style="height: 8px;">
                            <?php
                                $eventDate = $timeline->event->event_date;
                                $dueDate = $timeline->due_date;
                                $now = now();
                                
                                if ($eventDate && $dueDate) {
                                    $totalDays = $eventDate->diffInDays($dueDate);
                                    $passedDays = $now->diffInDays($dueDate);
                                    $progress = $totalDays > 0 ? min(100, ($passedDays / $totalDays) * 100) : 0;
                                } else {
                                    $progress = $timeline->is_completed ? 100 : 0;
                                }
                            ?>
                            <div class="progress-bar bg-<?php echo e($timeline->is_completed ? 'success' : ($progress > 100 ? 'danger' : 'primary')); ?>" 
                                 style="width: <?php echo e(min(100, $progress)); ?>%"></div>
                        </div>
                        <small class="text-muted">Tiến độ thời gian</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Hành động nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Hành động nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if(!$timeline->is_completed): ?>
                        <button class="btn btn-success" onclick="markAsCompleted(<?php echo e($timeline->id); ?>)">
                            <i class="fas fa-check me-2"></i>Đánh dấu hoàn thành
                        </button>
                    <?php else: ?>
                        <button class="btn btn-warning" onclick="markAsPending(<?php echo e($timeline->id); ?>)">
                            <i class="fas fa-undo me-2"></i>Đánh dấu chưa hoàn thành
                        </button>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('timelines.edit', $timeline)); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    
                    <button class="btn btn-outline-info" onclick="duplicateTimeline(<?php echo e($timeline->id); ?>)">
                        <i class="fas fa-copy me-2"></i>Nhân bản
                    </button>
                    
                    <a href="<?php echo e(route('events.show', $timeline->event)); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-calendar me-2"></i>Xem sự kiện
                    </a>
                    
                    <button class="btn btn-outline-primary" onclick="exportTimeline()">
                        <i class="fas fa-download me-2"></i>Xuất báo cáo
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Thông tin meta -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Thông tin hệ thống</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Tạo lúc:</small><br>
                    <?php echo e($timeline->created_at->format('d/m/Y H:i')); ?>

                    <small class="text-muted">(<?php echo e($timeline->created_at->diffForHumans()); ?>)</small>
                </div>
                
                <?php if($timeline->updated_at != $timeline->created_at): ?>
                    <div class="mb-2">
                        <small class="text-muted">Cập nhật lần cuối:</small><br>
                        <?php echo e($timeline->updated_at->format('d/m/Y H:i')); ?>

                        <small class="text-muted">(<?php echo e($timeline->updated_at->diffForHumans()); ?>)</small>
                    </div>
                <?php endif; ?>
                
                <?php if($timeline->is_completed && $timeline->completed_at): ?>
                    <div class="mb-2">
                        <small class="text-muted">Hoàn thành lúc:</small><br>
                        <?php echo e($timeline->completed_at->format('d/m/Y H:i')); ?>

                        <small class="text-muted">(<?php echo e($timeline->completed_at->diffForHumans()); ?>)</small>
                    </div>
                <?php endif; ?>
                
                <div class="mb-2">
                    <small class="text-muted">ID:</small> #<?php echo e($timeline->id); ?>

                </div>
                
                <?php if($timeline->send_notification): ?>
                    <div class="mb-2">
                        <span class="badge bg-success">
                            <i class="fas fa-bell me-1"></i>Thông báo được bật
                        </span>
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
                <p>Bạn có chắc chắn muốn xóa mốc thời gian <strong>"<?php echo e($timeline->title); ?>"</strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="<?php echo e(route('timelines.destroy', $timeline)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Xóa mốc thời gian
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.timeline-related {
    position: relative;
    padding-left: 30px;
}

.timeline-related::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 8px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #6c757d;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item.current .timeline-marker {
    background: #0d6efd;
    box-shadow: 0 0 0 2px #0d6efd;
}

.timeline-item.completed .timeline-marker {
    background: #198754;
    box-shadow: 0 0 0 2px #198754;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}

.timeline-item.current .timeline-content {
    background: #e7f3ff;
    border-left-color: #0d6efd;
}

.timeline-item.completed .timeline-content {
    background: #e8f5e8;
    border-left-color: #198754;
}

/* Toast notifications */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    min-width: 300px;
    transform: translateX(400px);
    transition: transform 0.3s ease-in-out;
}

.toast-notification.show {
    transform: translateX(0);
}

.toast-content {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background: white;
    border-left: 4px solid;
}

.toast-success .toast-content {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%);
}

.toast-error .toast-content {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #ffffff 100%);
}

.toast-warning .toast-content {
    border-left-color: #ffc107;
    background: linear-gradient(135deg, #fff3cd 0%, #ffffff 100%);
}

.toast-info .toast-content {
    border-left-color: #17a2b8;
    background: linear-gradient(135deg, #d1ecf1 0%, #ffffff 100%);
}

.toast-icon {
    margin-right: 12px;
    font-size: 20px;
}

.toast-success .toast-icon {
    color: #28a745;
}

.toast-error .toast-icon {
    color: #dc3545;
}

.toast-warning .toast-icon {
    color: #ffc107;
}

.toast-info .toast-icon {
    color: #17a2b8;
}

.toast-message {
    flex: 1;
    font-weight: 500;
    color: #333;
}

.toast-close {
    background: none;
    border: none;
    font-size: 20px;
    font-weight: bold;
    color: #666;
    cursor: pointer;
    padding: 0;
    margin-left: 15px;
    line-height: 1;
}

.toast-close:hover {
    color: #000;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Mark timeline as completed
function markAsCompleted(timelineId) {
    if (confirm('Bạn có chắc chắn muốn đánh dấu mốc thời gian này là hoàn thành?')) {
        fetch(`/timelines/${timelineId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Có lỗi xảy ra khi cập nhật trạng thái!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi cập nhật trạng thái!', 'error');
        });
    }
}

// Mark timeline as pending
function markAsPending(timelineId) {
    if (confirm('Bạn có chắc chắn muốn đánh dấu mốc thời gian này là chưa hoàn thành?')) {
        fetch(`/timelines/${timelineId}/uncomplete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Có lỗi xảy ra khi cập nhật trạng thái!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi cập nhật trạng thái!', 'error');
        });
    }
}

// Duplicate timeline
function duplicateTimeline(timelineId) {
    if (confirm('Bạn có muốn tạo một bản sao của mốc thời gian này?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/timelines/${timelineId}/duplicate`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// Export timeline
function exportTimeline() {
    const timelineData = {
        title: '<?php echo e($timeline->title); ?>',
        event: '<?php echo e($timeline->event->name); ?>',
        description: '<?php echo e($timeline->description); ?>',
        due_date: '<?php echo e($timeline->due_date ? $timeline->due_date->format("d/m/Y") : ""); ?>',
        due_time: '<?php echo e($timeline->due_time ? $timeline->due_time->format("H:i") : ""); ?>',
        duration: '<?php echo e($timeline->duration); ?>',
        priority: '<?php echo e($timeline->priority); ?>',
        location: '<?php echo e($timeline->location); ?>',
        assigned_to: '<?php echo e($timeline->assigned_to); ?>',
        contact_info: '<?php echo e($timeline->contact_info); ?>',
        notes: '<?php echo e($timeline->notes); ?>',
        is_completed: <?php echo e($timeline->is_completed ? 'true' : 'false'); ?>,
        is_milestone: <?php echo e($timeline->is_milestone ? 'true' : 'false'); ?>,
        created_at: '<?php echo e($timeline->created_at->format("d/m/Y H:i")); ?>',
        updated_at: '<?php echo e($timeline->updated_at->format("d/m/Y H:i")); ?>'
    };
    
    let content = `BÁO CÁO MỐC THỜI GIAN\n`;
    content += `======================\n\n`;
    content += `Tiêu đề: ${timelineData.title}\n`;
    content += `Sự kiện: ${timelineData.event}\n`;
    content += `Mô tả: ${timelineData.description}\n`;
    content += `Ngày thực hiện: ${timelineData.due_date}${timelineData.due_time ? ` lúc ${timelineData.due_time}` : ''}\n`;
    
    if (timelineData.duration) {
        content += `Thời lượng: ${timelineData.duration} phút\n`;
    }
    
    content += `Độ ưu tiên: ${timelineData.priority}\n`;
    
    if (timelineData.location) {
        content += `Địa điểm: ${timelineData.location}\n`;
    }
    
    if (timelineData.assigned_to) {
        content += `Người phụ trách: ${timelineData.assigned_to}\n`;
        if (timelineData.contact_info) {
            content += `Liên hệ: ${timelineData.contact_info}\n`;
        }
    }
    
    content += `Trạng thái: ${timelineData.is_completed ? 'Hoàn thành' : 'Chưa hoàn thành'}\n`;
    content += `Mốc quan trọng: ${timelineData.is_milestone ? 'Có' : 'Không'}\n`;
    
    if (timelineData.notes) {
        content += `\nGhi chú:\n${timelineData.notes}\n`;
    }
    
    content += `\n======================\n`;
    content += `Tạo lúc: ${timelineData.created_at}\n`;
    content += `Cập nhật: ${timelineData.updated_at}\n`;
    content += `Xuất lúc: ${new Date().toLocaleString('vi-VN')}\n`;
    
    // Create and download file
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `timeline_${timelineData.title.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().split('T')[0]}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Toast notification function
function showToast(message, type = 'info') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-icon">
                ${getToastIcon(type)}
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;

    // Add to page
    document.body.appendChild(toast);

    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

function getToastIcon(type) {
    switch(type) {
        case 'success': return '<i class="fas fa-check-circle"></i>';
        case 'error': return '<i class="fas fa-exclamation-circle"></i>';
        case 'warning': return '<i class="fas fa-exclamation-triangle"></i>';
        default: return '<i class="fas fa-info-circle"></i>';
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\timelines\show.blade.php ENDPATH**/ ?>