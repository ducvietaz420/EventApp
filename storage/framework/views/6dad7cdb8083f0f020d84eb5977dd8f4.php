<?php $__env->startSection('title', 'Quản lý ngân sách'); ?>
<?php $__env->startSection('page-title', 'Quản lý ngân sách'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('budgets.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Thêm ngân sách
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Bộ lọc -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('budgets.index')); ?>" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo e(request('search')); ?>" placeholder="Tìm theo mô tả...">
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">Danh mục</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Tất cả</option>
                    <option value="venue" <?php echo e(request('category') === 'venue' ? 'selected' : ''); ?>>Địa điểm</option>
                    <option value="catering" <?php echo e(request('category') === 'catering' ? 'selected' : ''); ?>>Catering</option>
                    <option value="decoration" <?php echo e(request('category') === 'decoration' ? 'selected' : ''); ?>>Trang trí</option>
                    <option value="equipment" <?php echo e(request('category') === 'equipment' ? 'selected' : ''); ?>>Thiết bị</option>
                    <option value="marketing" <?php echo e(request('category') === 'marketing' ? 'selected' : ''); ?>>Marketing</option>
                    <option value="staff" <?php echo e(request('category') === 'staff' ? 'selected' : ''); ?>>Nhân sự</option>
                    <option value="transportation" <?php echo e(request('category') === 'transportation' ? 'selected' : ''); ?>>Vận chuyển</option>
                    <option value="other" <?php echo e(request('category') === 'other' ? 'selected' : ''); ?>>Khác</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="event_id" class="form-label">Sự kiện</label>
                <select class="form-select" id="event_id" name="event_id">
                    <option value="">Tất cả sự kiện</option>
                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($event->id); ?>" <?php echo e(request('event_id') == $event->id ? 'selected' : ''); ?>>
                            <?php echo e($event->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="min_amount" class="form-label">Từ (VNĐ)</label>
                <input type="number" class="form-control" id="min_amount" name="min_amount" 
                       value="<?php echo e(request('min_amount')); ?>" placeholder="0">
            </div>
            <div class="col-md-2">
                <label for="max_amount" class="form-label">Đến (VNĐ)</label>
                <input type="number" class="form-control" id="max_amount" name="max_amount" 
                       value="<?php echo e(request('max_amount')); ?>" placeholder="999999999">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Thống kê tổng quan -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h4><?php echo e(number_format($budgets->sum('estimated_cost'), 0, ',', '.')); ?></h4>
                <p class="mb-0">Tổng ngân sách (VNĐ)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h4><?php echo e(number_format($budgets->sum('actual_cost'), 0, ',', '.')); ?></h4>
                <p class="mb-0">Đã chi tiêu (VNĐ)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h4><?php echo e(number_format($budgets->sum('estimated_cost') - $budgets->sum('actual_cost'), 0, ',', '.')); ?></h4>
                <p class="mb-0">Còn lại (VNĐ)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h4><?php echo e($budgets->count()); ?></h4>
                <p class="mb-0">Tổng số khoản</p>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách ngân sách -->
<div class="card shadow">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Danh sách ngân sách (<?php echo e($budgets->total()); ?> kết quả)</h6>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" onclick="exportBudgets()">
                    <i class="fas fa-download me-1"></i>Xuất Excel
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if($budgets->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sự kiện</th>
                            <th>Danh mục</th>
                            <th>Mô tả</th>
                            <th class="text-end">Ngân sách</th>
                            <th class="text-end">Đã chi</th>
                            <th class="text-end">Còn lại</th>
                            <th class="text-center">Tiến độ</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $remaining = $budget->estimated_cost - $budget->actual_cost;
                                $percentage = $budget->estimated_cost > 0 ? ($budget->actual_cost / $budget->estimated_cost) * 100 : 0;
                                $progressClass = $percentage > 100 ? 'bg-danger' : ($percentage > 80 ? 'bg-warning' : 'bg-success');
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('events.show', $budget->event_id)); ?>" class="text-decoration-none">
                                        <?php echo e($budget->event->name); ?>

                                    </a>
                                    <br>
                                    <small class="text-muted"><?php echo e($budget->event->type_display); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo e($budget->category_display); ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo e($budget->description); ?></div>
                                  
                                </td>
                                <td class="text-end">
                                    <strong><?php echo e(number_format($budget->estimated_cost, 0, ',', '.')); ?></strong>
                                    <small class="text-muted d-block">VNĐ</small>
                                </td>
                                <td class="text-end">
                                    <span class="<?php echo e($percentage > 100 ? 'text-danger' : 'text-primary'); ?>">
                                        <strong><?php echo e(number_format($budget->actual_cost, 0, ',', '.')); ?></strong>
                                    </span>
                                    <small class="text-muted d-block">VNĐ</small>
                                </td>
                                <td class="text-end">
                                    <span class="<?php echo e($remaining < 0 ? 'text-danger' : 'text-success'); ?>">
                                        <strong><?php echo e(number_format($remaining, 0, ',', '.')); ?></strong>
                                    </span>
                                    <small class="text-muted d-block">VNĐ</small>
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 8px; width: 60px; margin: 0 auto;">
                                        <div class="progress-bar <?php echo e($progressClass); ?>" 
                                             style="width: <?php echo e(min($percentage, 100)); ?>%"
                                             title="<?php echo e(round($percentage, 1)); ?>%">
                                        </div>
                                    </div>
                                    <small class="text-muted"><?php echo e(round($percentage, 1)); ?>%</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('budgets.show', $budget->id)); ?>" 
                                           class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('budgets.edit', $budget->id)); ?>" 
                                           class="btn btn-outline-warning" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteBudget(<?php echo e($budget->id); ?>, '<?php echo e($budget->description); ?>')" 
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <div class="card-footer">
                <?php echo e($budgets->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-dollar-sign fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không tìm thấy ngân sách nào</h5>
                <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc tạo ngân sách mới.</p>
                <a href="<?php echo e(route('budgets.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tạo ngân sách đầu tiên
                </a>
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
                <p>Bạn có chắc chắn muốn xóa khoản ngân sách <strong id="budgetDescription"></strong>?</p>
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
function deleteBudget(budgetId, description) {
    document.getElementById('budgetDescription').textContent = description;
    document.getElementById('deleteForm').action = `/budgets/${budgetId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function exportBudgets() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = `<?php echo e(route('budgets.index')); ?>?${params.toString()}`;
}

// Auto-submit form when filters change
document.querySelectorAll('#category, #event_id').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});

// Clear filters
function clearFilters() {
    window.location.href = '<?php echo e(route('budgets.index')); ?>';
}

// Add clear filters button if any filter is active
if (window.location.search) {
    const clearBtn = document.createElement('button');
    clearBtn.type = 'button';
    clearBtn.className = 'btn btn-outline-secondary';
    clearBtn.innerHTML = '<i class="fas fa-times me-1"></i>Xóa bộ lọc';
    clearBtn.onclick = clearFilters;
    
    const actionsDiv = document.querySelector('.card-header .btn-group');
    actionsDiv.appendChild(clearBtn);
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\budgets\index.blade.php ENDPATH**/ ?>