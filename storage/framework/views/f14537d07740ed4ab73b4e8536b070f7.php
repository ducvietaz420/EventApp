<?php $__env->startSection('title', 'Danh sách công việc'); ?>
<?php $__env->startSection('page-title', 'Quản lý danh sách công việc'); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="btn-group" role="group">
        <a href="<?php echo e(route('checklists.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm công việc
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-download me-2"></i>Xuất dữ liệu
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportToExcel()">
                    <i class="fas fa-file-excel text-success me-2"></i>Xuất Excel
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf text-danger me-2"></i>Xuất PDF
                </a></li>
            </ul>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo e($stats['total'] ?? 0); ?></h4>
                        <p class="mb-0">Tổng công việc</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo e($stats['completed'] ?? 0); ?></h4>
                        <p class="mb-0">Hoàn thành</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo e($stats['pending'] ?? 0); ?></h4>
                        <p class="mb-0">Đang chờ</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo e($stats['overdue'] ?? 0); ?></h4>
                        <p class="mb-0">Quá hạn</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc và tìm kiếm -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="mb-0">Bộ lọc và tìm kiếm</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('checklists.index')); ?>" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo e(request('search')); ?>" placeholder="Tìm theo tên, mô tả...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Sự kiện</label>
                        <select class="form-select" id="event_id" name="event_id" onchange="autoSubmit()">
                            <option value="">Tất cả sự kiện</option>
                            <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>>
                                    <?php echo e($event->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status" onchange="autoSubmit()">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Đang chờ</option>
                            <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>Đang thực hiện</option>
                            <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Hoàn thành</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="priority" class="form-label">Độ ưu tiên</label>
                        <select class="form-select" id="priority" name="priority" onchange="autoSubmit()">
                            <option value="">Tất cả độ ưu tiên</option>
                            <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Thấp</option>
                            <option value="medium" <?php echo e(request('priority') === 'medium' ? 'selected' : ''); ?>>Trung bình</option>
                            <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>Cao</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Người phụ trách</label>
                        <select class="form-select" id="assigned_to" name="assigned_to" onchange="autoSubmit()">
                            <option value="">Tất cả</option>
                            <?php $__currentLoopData = $assignees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($assignee); ?>" <?php echo e(request('assigned_to') === $assignee ? 'selected' : ''); ?>>
                                    <?php echo e($assignee); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()" title="Xóa bộ lọc">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="due_date_from" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="due_date_from" name="due_date_from" 
                               value="<?php echo e(request('due_date_from')); ?>" onchange="autoSubmit()">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="due_date_to" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="due_date_to" name="due_date_to" 
                               value="<?php echo e(request('due_date_to')); ?>" onchange="autoSubmit()">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="sort_by" class="form-label">Sắp xếp theo</label>
                        <select class="form-select" id="sort_by" name="sort_by" onchange="autoSubmit()">
                            <option value="due_date" <?php echo e(request('sort_by', 'due_date') === 'due_date' ? 'selected' : ''); ?>>Ngày hạn</option>
                            <option value="priority" <?php echo e(request('sort_by') === 'priority' ? 'selected' : ''); ?>>Độ ưu tiên</option>
                            <option value="created_at" <?php echo e(request('sort_by') === 'created_at' ? 'selected' : ''); ?>>Ngày tạo</option>
                            <option value="title" <?php echo e(request('sort_by') === 'title' ? 'selected' : ''); ?>>Tên công việc</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="sort_direction" class="form-label">Thứ tự</label>
                        <select class="form-select" id="sort_direction" name="sort_direction" onchange="autoSubmit()">
                            <option value="asc" <?php echo e(request('sort_direction', 'asc') === 'asc' ? 'selected' : ''); ?>>Tăng dần</option>
                            <option value="desc" <?php echo e(request('sort_direction') === 'desc' ? 'selected' : ''); ?>>Giảm dần</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Hành động hàng loạt -->
<div class="card shadow mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span id="selectedCount">0</span> công việc được chọn
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-success" onclick="bulkMarkCompleted()">
                    <i class="fas fa-check me-2"></i>Đánh dấu hoàn thành
                </button>
                <button type="button" class="btn btn-warning" onclick="bulkChangePriority()">
                    <i class="fas fa-flag me-2"></i>Thay đổi độ ưu tiên
                </button>
                <button type="button" class="btn btn-info" onclick="bulkAssign()">
                    <i class="fas fa-user me-2"></i>Gán người phụ trách
                </button>
                <button type="button" class="btn btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash me-2"></i>Xóa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách công việc -->
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Danh sách công việc (<?php echo e($checklists->total()); ?> kết quả)</h6>
        <div class="btn-group btn-group-sm" role="group">
            <input type="radio" class="btn-check" name="viewMode" id="listView" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="listView">
                <i class="fas fa-list"></i> Danh sách
            </label>
            
            <input type="radio" class="btn-check" name="viewMode" id="cardView" autocomplete="off">
            <label class="btn btn-outline-primary" for="cardView">
                <i class="fas fa-th-large"></i> Thẻ
            </label>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- List View -->
        <div id="listViewContent">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </div>
                            </th>
                            <th>Công việc</th>
                            <th>Sự kiện</th>
                            <th>Người phụ trách</th>
                            <th>Ngày hạn</th>
                            <th>Độ ưu tiên</th>
                            <th>Trạng thái</th>
                            <th>Tiến độ</th>
                            <th width="120">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $checklists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $checklist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="<?php echo e($checklist->is_completed ? 'table-success' : ($checklist->due_date && $checklist->due_date->isPast() ? 'table-warning' : '')); ?>">
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input item-checkbox" type="checkbox" 
                                               value="<?php echo e($checklist->id); ?>" onchange="updateBulkActions()">
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="<?php echo e(route('checklists.show', $checklist)); ?>" class="text-decoration-none fw-bold">
                                            <?php echo e($checklist->title); ?>

                                        </a>
                                        <?php if($checklist->description): ?>
                                            <br><small class="text-muted"><?php echo e(Str::limit($checklist->description, 50)); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('events.show', $checklist->event)); ?>" class="text-decoration-none">
                                        <?php echo e($checklist->event->name); ?>

                                    </a>
                                    <br><small class="text-muted"><?php echo e(ucfirst($checklist->event->type)); ?></small>
                                </td>
                                <td>
                                    <?php if($checklist->assigned_to): ?>
                                        <div>
                                            <i class="fas fa-user text-muted me-1"></i><?php echo e($checklist->assigned_to); ?>

                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa gán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($checklist->due_date): ?>
                                        <div>
                                            <?php echo e($checklist->due_date->format('d/m/Y')); ?>

                                            <?php if($checklist->due_time): ?>
                                                <br><small class="text-muted"><?php echo e($checklist->due_time->format('H:i')); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($checklist->due_date->isToday()): ?>
                                            <span class="badge bg-warning">Hôm nay</span>
                                        <?php elseif($checklist->due_date->isTomorrow()): ?>
                                            <span class="badge bg-info">Ngày mai</span>
                                        <?php elseif($checklist->due_date->isPast() && !$checklist->is_completed): ?>
                                            <span class="badge bg-danger">Quá hạn</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Không xác định</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e($checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                        <?php switch($checklist->priority):
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
                                </td>
                                <td>
                                    <?php if($checklist->is_completed): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Hoàn thành
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Đang chờ
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $progress = $checklist->is_completed ? 100 : 0;
                                    ?>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-<?php echo e($progress === 100 ? 'success' : 'primary'); ?>" 
                                             style="width: <?php echo e($progress); ?>%"><?php echo e($progress); ?>%</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo e(route('checklists.show', $checklist)); ?>" class="btn btn-outline-info" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('checklists.edit', $checklist)); ?>" class="btn btn-outline-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if(!$checklist->is_completed): ?>
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="markCompleted(<?php echo e($checklist->id); ?>)" title="Hoàn thành">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteChecklist(<?php echo e($checklist->id); ?>)" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-tasks fa-3x mb-3"></i>
                                        <h5>Không có công việc nào</h5>
                                        <p>Hãy thêm công việc đầu tiên cho sự kiện của bạn.</p>
                                        <a href="<?php echo e(route('checklists.create')); ?>" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Thêm công việc
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Card View -->
        <div id="cardViewContent" style="display: none;">
            <div class="p-3">
                <div class="row">
                    <?php $__empty_1 = true; $__currentLoopData = $checklists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $checklist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 <?php echo e($checklist->is_completed ? 'border-success' : ($checklist->due_date && $checklist->due_date->isPast() ? 'border-warning' : '')); ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input item-checkbox" type="checkbox" 
                                               value="<?php echo e($checklist->id); ?>" onchange="updateBulkActions()">
                                    </div>
                                    <span class="badge bg-<?php echo e($checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                        <?php echo e(ucfirst($checklist->priority)); ?>

                                    </span>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="<?php echo e(route('checklists.show', $checklist)); ?>" class="text-decoration-none">
                                            <?php echo e($checklist->title); ?>

                                        </a>
                                    </h6>
                                    <?php if($checklist->description): ?>
                                        <p class="card-text text-muted small"><?php echo e(Str::limit($checklist->description, 80)); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Sự kiện:</small><br>
                                        <a href="<?php echo e(route('events.show', $checklist->event)); ?>" class="text-decoration-none">
                                            <?php echo e($checklist->event->name); ?>

                                        </a>
                                    </div>
                                    
                                    <?php if($checklist->assigned_to): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Người phụ trách:</small><br>
                                            <i class="fas fa-user text-muted me-1"></i><?php echo e($checklist->assigned_to); ?>

                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($checklist->due_date): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Hạn:</small><br>
                                            <?php echo e($checklist->due_date->format('d/m/Y')); ?>

                                            <?php if($checklist->due_time): ?>
                                                <?php echo e($checklist->due_time->format('H:i')); ?>

                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="progress mb-2" style="height: 8px;">
                                        <?php $progress = $checklist->is_completed ? 100 : 0; ?>
                                        <div class="progress-bar bg-<?php echo e($progress === 100 ? 'success' : 'primary'); ?>" 
                                             style="width: <?php echo e($progress); ?>%"></div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if($checklist->is_completed): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Hoàn thành
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Đang chờ
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo e(route('checklists.edit', $checklist)); ?>" class="btn btn-outline-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if(!$checklist->is_completed): ?>
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="markCompleted(<?php echo e($checklist->id); ?>)" title="Hoàn thành">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <h5>Không có công việc nào</h5>
                                <p class="text-muted">Hãy thêm công việc đầu tiên cho sự kiện của bạn.</p>
                                <a href="<?php echo e(route('checklists.create')); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Thêm công việc
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($checklists->hasPages()): ?>
        <div class="card-footer">
            <?php echo e($checklists->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Auto submit form when filters change
function autoSubmit() {
    document.getElementById('filterForm').submit();
}

// Clear all filters
function clearFilters() {
    const form = document.getElementById('filterForm');
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
    form.submit();
}

// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkCard = document.getElementById('bulkActionsCard');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkCard.style.display = 'block';
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkCard.style.display = 'none';
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
}

// Switch between list and card view
document.getElementById('listView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('listViewContent').style.display = 'block';
        document.getElementById('cardViewContent').style.display = 'none';
    }
});

document.getElementById('cardView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('listViewContent').style.display = 'none';
        document.getElementById('cardViewContent').style.display = 'block';
    }
});

// Mark checklist as completed
function markCompleted(checklistId) {
    if (confirm('Bạn có chắc chắn muốn đánh dấu công việc này là hoàn thành?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}/complete`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete checklist
function deleteChecklist(checklistId) {
    if (confirm('Bạn có chắc chắn muốn xóa công việc này? Hành động này không thể hoàn tác!')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/checklists/${checklistId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk actions
function bulkMarkCompleted() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn đánh dấu ${selected.length} công việc là hoàn thành?`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-complete';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkChangePriority() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    const priority = prompt('Chọn độ ưu tiên mới (low/medium/high):', 'medium');
    if (priority && ['low', 'medium', 'high'].includes(priority)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-priority';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const priorityInput = document.createElement('input');
        priorityInput.type = 'hidden';
        priorityInput.name = 'priority';
        priorityInput.value = priority;
        form.appendChild(priorityInput);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkAssign() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    const assignee = prompt('Nhập tên người phụ trách:');
    if (assignee) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-assign';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const assigneeInput = document.createElement('input');
        assigneeInput.type = 'hidden';
        assigneeInput.name = 'assigned_to';
        assigneeInput.value = assignee;
        form.appendChild(assigneeInput);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkDelete() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất một công việc!');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn xóa ${selected.length} công việc? Hành động này không thể hoàn tác!`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/checklists/bulk-delete';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Export functions
function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = '<?php echo e(route("checklists.index")); ?>?' + params.toString();
}

function exportToPDF() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'pdf');
    window.location.href = '<?php echo e(route("checklists.index")); ?>?' + params.toString();
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\checklists\index.blade.php ENDPATH**/ ?>