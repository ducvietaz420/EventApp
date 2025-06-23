<?php $__env->startSection('title', 'Danh sách sự kiện'); ?>
<?php $__env->startSection('page-title', 'Quản lý sự kiện'); ?>

<?php $__env->startSection('page-actions'); ?>
    <?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->hasPermission('events.create')): ?>
            <a href="<?php echo e(route('events.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
            </a>
        <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách sự kiện</h6>
                </div>
                <div class="col-auto">
                    <!-- Bộ lọc và tìm kiếm -->
                    <form method="GET" action="<?php echo e(route('events.index')); ?>" class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sự kiện..."
                                value="<?php echo e(request('search')); ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <select name="status" class="form-select form-select-sm" style="width: 150px;"
                            onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            <option value="planning" <?php echo e(request('status') === 'planning' ? 'selected' : ''); ?>>Đang lên kế
                                hoạch</option>
                            <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>Đang tiến
                                hành</option>
                            <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Hoàn thành
                            </option>
                            <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Đã hủy
                            </option>
                        </select>

                        <select name="type" class="form-select form-select-sm" style="width: 150px;"
                            onchange="this.form.submit()">
                            <option value="">Tất cả loại</option>
                            <option value="conference" <?php echo e(request('type') === 'conference' ? 'selected' : ''); ?>>Hội nghị
                            </option>
                            <option value="wedding" <?php echo e(request('type') === 'wedding' ? 'selected' : ''); ?>>Đám cưới</option>
                            <option value="corporate" <?php echo e(request('type') === 'corporate' ? 'selected' : ''); ?>>Doanh nghiệp
                            </option>
                            <option value="workshop" <?php echo e(request('type') === 'workshop' ? 'selected' : ''); ?>>Workshop</option>
                            <option value="seminar" <?php echo e(request('type') === 'seminar' ? 'selected' : ''); ?>>Seminar</option>
                            <option value="party" <?php echo e(request('type') === 'party' ? 'selected' : ''); ?>>Tiệc</option>
                            <option value="exhibition" <?php echo e(request('type') === 'exhibition' ? 'selected' : ''); ?>>Triển lãm
                            </option>
                            <option value="other" <?php echo e(request('type') === 'other' ? 'selected' : ''); ?>>Khác</option>
                        </select>

                        <?php if(request()->hasAny(['search', 'status', 'type'])): ?>
                            <a href="<?php echo e(route('events.index')); ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if(isset($events) && $events->count() > 0): ?>
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
                            <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                                    <a href="<?php echo e(route('events.show', $event->id)); ?>" class="text-decoration-none">
                                                        <?php echo e($event->name); ?>

                                                    </a>
                                                </h6>
                                                <small class="text-muted"><?php echo e(Str::limit($event->description, 50)); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($event->type_display); ?></span>
                                    </td>
                                    <td>
                                        <?php if($event->event_date): ?>
                                            <div>
                                                <strong><?php echo e($event->event_date->format('d/m/Y')); ?></strong><br>
                                                <!-- <small class="text-muted"><?php echo e($event->event_date->format('H:i')); ?></small> -->
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa xác định</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($event->venue): ?>
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            <?php echo e(Str::limit($event->venue, 30)); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Chưa xác định</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?php echo e(route('events.updateStatus', $event->id)); ?>" method="POST"
                                            class="status-update-form">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <select name="status"
                                                class="form-select form-select-sm status-badge status-<?php echo e($event->status); ?>"
                                                onchange="this.form.submit()">
                                                <option value="planning" <?php echo e($event->status === 'planning' ? 'selected' : ''); ?>>Đang
                                                    lên kế hoạch</option>
                                                <option value="confirmed" <?php echo e($event->status === 'confirmed' ? 'selected' : ''); ?>>Đã
                                                    xác nhận</option>
                                                <option value="in_progress" <?php echo e($event->status === 'in_progress' ? 'selected' : ''); ?>>
                                                    Đang tiến hành</option>
                                                <option value="completed" <?php echo e($event->status === 'completed' ? 'selected' : ''); ?>>Hoàn
                                                    thành</option>
                                                <option value="cancelled" <?php echo e($event->status === 'cancelled' ? 'selected' : ''); ?>>Đã
                                                    hủy</option>
                                            </select>
                                        </form>
                                    </td>
                                   
                                    <td>
                                        <?php if($event->checklists && $event->checklists->count() > 0): ?>
                                            <?php
                                                $totalTasks = $event->checklists->count();
                                                $completedTasks = $event->checklists->whereNotNull('completed_at')->count();
                                                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                            ?>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?php echo e($progress); ?>%"
                                                    aria-valuenow="<?php echo e($progress); ?>" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted"><?php echo e($completedTasks); ?>/<?php echo e($totalTasks); ?> nhiệm vụ</small>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa có nhiệm vụ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <?php if(auth()->guard()->check()): ?>
                                                <?php if(auth()->user()->hasPermission('events.view')): ?>
                                                    <a href="<?php echo e(route('events.show', $event->id)); ?>" class="btn btn-sm btn-outline-primary"
                                                        title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if(auth()->user()->hasPermission('events.edit')): ?>
                                                    <a href="<?php echo e(route('events.edit', $event->id)); ?>" class="btn btn-sm btn-outline-warning"
                                                        title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if(auth()->user()->hasPermission('events.delete')): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Xóa"
                                                        onclick="deleteEvent(<?php echo e($event->id); ?>, '<?php echo e($event->name); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($events->hasPages()): ?>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Hiển thị <?php echo e($events->firstItem()); ?> đến <?php echo e($events->lastItem()); ?>

                                trong tổng số <?php echo e($events->total()); ?> sự kiện
                            </div>
                            <div>
                                <?php echo e($events->appends(request()->query())->links()); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có sự kiện nào</h5>
                    <p class="text-muted mb-4">
                        <?php if(request()->hasAny(['search', 'status', 'type'])): ?>
                            Không tìm thấy sự kiện phù hợp với bộ lọc.
                        <?php else: ?>
                            Bạn chưa tạo sự kiện nào. Hãy tạo sự kiện đầu tiên!
                        <?php endif; ?>
                    </p>
                    <?php if(request()->hasAny(['search', 'status', 'type'])): ?>
                        <a href="<?php echo e(route('events.index')); ?>" class="btn btn-outline-primary me-2">
                            <i class="fas fa-times me-2"></i>Xóa bộ lọc
                        </a>
                    <?php endif; ?>
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->hasPermission('events.create')): ?>
                            <a href="<?php echo e(route('events.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views/events/index.blade.php ENDPATH**/ ?>