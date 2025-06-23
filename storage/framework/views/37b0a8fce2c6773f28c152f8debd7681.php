<?php $__env->startSection('title', 'Quản lý người dùng'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-users text-primary me-2"></i>
                        Quản lý người dùng
                    </h2>
                    <p class="text-muted mb-0">Quản lý tài khoản và phân quyền người dùng</p>
                </div>
                <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm người dùng
                </a>
            </div>

            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('users.index')); ?>" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="<?php echo e(request('search')); ?>" 
                                   placeholder="Tên hoặc username...">
                        </div>
                        <div class="col-md-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin" <?php echo e(request('role') == 'admin' ? 'selected' : ''); ?>>Quản trị viên</option>
                                <option value="user" <?php echo e(request('role') == 'user' ? 'selected' : ''); ?>>Người dùng</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Hoạt động</option>
                                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Tạm khóa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-1"></i>Lọc
                                </button>
                                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if($users->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Người dùng</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Người tạo</th>
                                        <th>Đăng nhập cuối</th>
                                        <th>Ngày tạo</th>
                                        <th width="180">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold"><?php echo e($user->name); ?></div>
                                                        <div class="text-primary fw-medium">
                                                            <?php if($user->username): ?>
                                                                &#64;<?php echo e($user->username); ?>

                                                            <?php else: ?>
                                                                <span class="text-muted">Chưa có username</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo e($user->role === 'admin' ? 'bg-danger' : 'bg-primary'); ?>">
                                                    <i class="fas <?php echo e($user->role === 'admin' ? 'fa-crown' : 'fa-user'); ?> me-1"></i>
                                                    <?php echo e($user->role_display); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo e($user->status === 'active' ? 'bg-success' : 'bg-secondary'); ?>">
                                                    <i class="fas <?php echo e($user->status === 'active' ? 'fa-check' : 'fa-ban'); ?> me-1"></i>
                                                    <?php echo e($user->status_display); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php if($user->creator): ?>
                                                    <small><?php echo e($user->creator->name); ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Hệ thống</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($user->last_login_at): ?>
                                                    <small><?php echo e($user->last_login_at->format('d/m/Y H:i')); ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Chưa đăng nhập</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo e($user->created_at->format('d/m/Y')); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('users.permissions', $user)); ?>" class="btn btn-sm btn-outline-warning" title="Phân quyền">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </a>
                                                    
                                                    <?php if($user->id !== auth()->id()): ?>
                                                        <form method="POST" action="<?php echo e(route('users.toggle-status', $user)); ?>" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PATCH'); ?>
                                                            <button type="submit" 
                                                                    class="btn btn-sm <?php echo e($user->isActive() ? 'btn-outline-warning' : 'btn-outline-success'); ?>" 
                                                                    title="<?php echo e($user->isActive() ? 'Tạm khóa' : 'Kích hoạt'); ?>"
                                                                    onclick="return confirm('Bạn có chắc muốn <?php echo e($user->isActive() ? 'tạm khóa' : 'kích hoạt'); ?> tài khoản này?')">
                                                                <i class="fas <?php echo e($user->isActive() ? 'fa-ban' : 'fa-check'); ?>"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Xóa"
                                                                    onclick="return confirm('Bạn có chắc muốn xóa tài khoản <?php echo e($user->name); ?>? Hành động này không thể hoàn tác!')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Hiển thị <?php echo e($users->firstItem()); ?>-<?php echo e($users->lastItem()); ?> trong <?php echo e($users->total()); ?> người dùng
                            </div>
                            <?php echo e($users->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không tìm thấy người dùng nào</h5>
                            <p class="text-muted">Thử thay đổi bộ lọc hoặc tạo người dùng mới</p>
                            <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Thêm người dùng đầu tiên
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views/users/index.blade.php ENDPATH**/ ?>