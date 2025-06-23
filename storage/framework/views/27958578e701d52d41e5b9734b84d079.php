

<?php $__env->startSection('title', 'Chi tiết hoạt động'); ?>
<?php $__env->startSection('page-title', 'Chi tiết hoạt động'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('activity-logs.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Quay lại
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Thông tin chi tiết hoạt động
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Thông tin cơ bản -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Thông tin cơ bản</h6>
                        
                        <div class="mb-3">
                            <label class="fw-bold">Người thực hiện:</label>
                            <div class="d-flex align-items-center mt-1">
                                <div class="avatar-circle me-2" style="background: #6c757d;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div><?php echo e($activityLog->user->name ?? 'Người dùng đã xóa'); ?></div>
                                    <small class="text-muted"><?php echo e($activityLog->user->email ?? ''); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Hành động:</label>
                            <div class="mt-1">
                                <span class="badge bg-<?php echo e($activityLog->action_class); ?> fs-6">
                                    <i class="<?php echo e($activityLog->action_icon); ?> me-1"></i>
                                    <?php echo e($activityLog->action_display); ?>

                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Thời gian:</label>
                            <div class="mt-1">
                                <?php echo e($activityLog->created_at->format('d/m/Y H:i:s')); ?><br>
                                <small class="text-muted"><?php echo e($activityLog->created_at->diffForHumans()); ?></small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Mô tả:</label>
                            <div class="mt-1"><?php echo e($activityLog->description); ?></div>
                        </div>
                    </div>

                    <!-- Thông tin kỹ thuật -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Thông tin kỹ thuật</h6>
                        
                        <div class="mb-3">
                            <label class="fw-bold">Địa chỉ IP:</label>
                            <div class="mt-1">
                                <code><?php echo e($activityLog->ip_address ?? 'Không xác định'); ?></code>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">User Agent:</label>
                            <div class="mt-1">
                                <small class="text-muted"><?php echo e($activityLog->user_agent ?? 'Không xác định'); ?></small>
                            </div>
                        </div>

                        <?php if($activityLog->model_type && $activityLog->model_id): ?>
                            <div class="mb-3">
                                <label class="fw-bold">Đối tượng tác động:</label>
                                <div class="mt-1">
                                    <span class="badge bg-info"><?php echo e(class_basename($activityLog->model_type)); ?></span>
                                    <code>#<?php echo e($activityLog->model_id); ?></code>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Thay đổi dữ liệu -->
                <?php if($activityLog->old_values || $activityLog->new_values): ?>
                    <hr>
                    <h6 class="text-muted mb-3">Chi tiết thay đổi</h6>
                    
                    <div class="row">
                        <?php if($activityLog->old_values): ?>
                            <div class="col-md-6">
                                <h6 class="text-danger">
                                    <i class="fas fa-minus-circle me-1"></i>Giá trị cũ
                                </h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0"><code><?php echo e(json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if($activityLog->new_values): ?>
                            <div class="col-md-6">
                                <h6 class="text-success">
                                    <i class="fas fa-plus-circle me-1"></i>Giá trị mới
                                </h6>
                                <div class="bg-light p-3 rounded">
                                    <pre class="mb-0"><code><?php echo e(json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Timeline style display -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-timeline me-2"></i>Timeline hoạt động
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-<?php echo e($activityLog->action_class); ?>">
                            <i class="<?php echo e($activityLog->action_icon); ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1"><?php echo e($activityLog->action_display); ?></h6>
                            <p class="mb-1"><?php echo e($activityLog->description); ?></p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i><?php echo e($activityLog->created_at->format('d/m/Y H:i:s')); ?>

                                <i class="fas fa-map-marker-alt ms-3 me-1"></i><?php echo e($activityLog->ip_address); ?>

                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }

    .timeline {
        position: relative;
        padding-left: 3rem;
    }

    .timeline::before {
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
        margin-bottom: 2rem;
    }

    .timeline-marker {
        position: absolute;
        left: -52px;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        border: 3px solid #fff;
        box-shadow: 0 0 0 3px #dee2e6;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 3px solid #dee2e6;
    }

    pre code {
        font-size: 0.875rem;
        color: #495057;
    }
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views/activity-logs/show.blade.php ENDPATH**/ ?>