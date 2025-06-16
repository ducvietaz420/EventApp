<?php $__env->startSection('title', 'Chỉnh sửa ngân sách'); ?>
<?php $__env->startSection('page-title', 'Chỉnh sửa ngân sách'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('budgets.show', $budget)); ?>" class="btn btn-info">
        <i class="fas fa-eye me-2"></i>Xem chi tiết
    </a>
    <a href="<?php echo e(route('budgets.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Chỉnh sửa thông tin ngân sách</h5>
                <div class="text-muted small">
                    <i class="fas fa-calendar me-1"></i>Tạo: <?php echo e($budget->created_at->format('d/m/Y H:i')); ?>

                    <?php if($budget->updated_at != $budget->created_at): ?>
                        <br><i class="fas fa-edit me-1"></i>Cập nhật: <?php echo e($budget->updated_at->format('d/m/Y H:i')); ?>

                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Thống kê nhanh -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-primary"><?php echo e(number_format($budget->estimated_cost)); ?> VNĐ</div>
                            <div class="small text-muted">Ngân sách</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-danger"><?php echo e(number_format($budget->actual_cost)); ?> VNĐ</div>
                            <div class="small text-muted">Đã chi</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-success"><?php echo e(number_format($budget->estimated_cost - $budget->actual_cost)); ?> VNĐ</div>
                            <div class="small text-muted">Còn lại</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                            <div class="h5 mb-1 text-info"><?php echo e($budget->estimated_cost > 0 ? number_format(($budget->actual_cost / $budget->estimated_cost) * 100, 1) : 0); ?>%</div>
                            <div class="small text-muted">Tiến độ</div>
                        </div>
                    </div>
                </div>
                
                <form action="<?php echo e(route('budgets.update', $budget)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Sự kiện <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['event_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="event_id" name="event_id" required>
                                    <option value="">Chọn sự kiện</option>
                                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($event->id); ?>" 
                                                <?php echo e(old('event_id', $budget->event_id) == $event->id ? 'selected' : ''); ?>

                                                data-event-type="<?php echo e($event->type); ?>">
                                            <?php echo e($event->name); ?> (<?php echo e(ucfirst($event->type)); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['event_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="category" name="category" required>
                                    <option value="">Chọn danh mục</option>
                                    <option value="venue" <?php echo e(old('category', $budget->category) === 'venue' ? 'selected' : ''); ?>>Địa điểm</option>
                                    <option value="catering" <?php echo e(old('category', $budget->category) === 'catering' ? 'selected' : ''); ?>>Catering</option>
                                    <option value="decoration" <?php echo e(old('category', $budget->category) === 'decoration' ? 'selected' : ''); ?>>Trang trí</option>
                                    <option value="equipment" <?php echo e(old('category', $budget->category) === 'equipment' ? 'selected' : ''); ?>>Thiết bị</option>
                                    <option value="marketing" <?php echo e(old('category', $budget->category) === 'marketing' ? 'selected' : ''); ?>>Marketing</option>
                                    <option value="staff" <?php echo e(old('category', $budget->category) === 'staff' ? 'selected' : ''); ?>>Nhân sự</option>
                                    <option value="transportation" <?php echo e(old('category', $budget->category) === 'transportation' ? 'selected' : ''); ?>>Vận chuyển</option>
                                    <option value="other" <?php echo e(old('category', $budget->category) === 'other' ? 'selected' : ''); ?>>Khác</option>
                                </select>
                                <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Tên khoản mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php $__errorArgs = ['item_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               id="item_name" name="item_name" value="<?php echo e(old('item_name', $budget->item_name)); ?>" 
                               placeholder="Ví dụ: Thuê địa điểm, Chi phí catering..." required>
                        <?php $__errorArgs = ['item_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Mô tả chi tiết về khoản chi này..." required><?php echo e(old('description', $budget->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estimated_cost" class="form-label">Số tiền ngân sách (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?php $__errorArgs = ['estimated_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="estimated_cost" name="estimated_cost" value="<?php echo e(old('estimated_cost', $budget->estimated_cost)); ?>" 
                                       min="0" step="1000" placeholder="0" required>
                                <?php $__errorArgs = ['estimated_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">Nhập số tiền dự kiến cho khoản chi này</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="actual_cost" class="form-label">Số tiền đã chi (VNĐ)</label>
                                <input type="number" class="form-control <?php $__errorArgs = ['actual_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="actual_cost" name="actual_cost" value="<?php echo e(old('actual_cost', $budget->actual_cost)); ?>" 
                                       min="0" step="1000" placeholder="0">
                                <?php $__errorArgs = ['actual_cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">Số tiền đã chi tiêu thực tế</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hiển thị số tiền còn lại -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" id="remainingAmount">
                                <i class="fas fa-calculator me-2"></i>
                                <strong>Số tiền còn lại:</strong> 
                                <span id="remainingValue"><?php echo e(number_format($budget->estimated_cost - $budget->actual_cost)); ?> VNĐ</span>
                                <span id="remainingPercent" class="ms-2">
                                    (<?php echo e($budget->estimated_cost > 0 ? number_format((($budget->estimated_cost - $budget->actual_cost) / $budget->estimated_cost) * 100, 1) : 0); ?>%)
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="allocated_date" class="form-label">Ngày phân bổ</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['allocated_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="allocated_date" name="allocated_date" 
                                       value="<?php echo e(old('allocated_date', $budget->allocated_date ? $budget->allocated_date->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['allocated_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deadline" class="form-label">Hạn sử dụng</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['deadline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="deadline" name="deadline" 
                                       value="<?php echo e(old('deadline', $budget->deadline ? $budget->deadline->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['deadline'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">Ngày cuối cùng có thể sử dụng ngân sách này</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Thông tin bổ sung về khoản ngân sách này..."><?php echo e(old('notes', $budget->notes)); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="<?php echo e(route('budgets.show', $budget)); ?>" class="btn btn-info">
                                <i class="fas fa-eye me-2"></i>Xem chi tiết
                            </a>
                            <a href="<?php echo e(route('budgets.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Khôi phục
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật ngân sách
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Lịch sử thay đổi -->
        <div class="card shadow mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử thay đổi</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Tạo ngân sách</h6>
                            <p class="mb-1">Ngân sách được tạo với số tiền <?php echo e(number_format($budget->estimated_cost)); ?> VNĐ</p>
                            <small class="text-muted"><?php echo e($budget->created_at->format('d/m/Y H:i')); ?></small>
                        </div>
                    </div>
                    
                    <?php if($budget->updated_at != $budget->created_at): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Cập nhật thông tin</h6>
                            <p class="mb-1">Thông tin ngân sách được cập nhật</p>
                            <small class="text-muted"><?php echo e($budget->updated_at->format('d/m/Y H:i')); ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($budget->actual_cost > 0): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Chi tiêu</h6>
                            <p class="mb-1">Đã chi <?php echo e(number_format($budget->actual_cost)); ?> VNĐ</p>
                            <small class="text-muted">Cập nhật lần cuối: <?php echo e($budget->updated_at->format('d/m/Y H:i')); ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.timeline {
    position: relative;
    padding-left: 30px;
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
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Store original values for reset functionality
const originalValues = {
    event_id: '<?php echo e($budget->event_id); ?>',
    category: '<?php echo e($budget->category); ?>',
    description: '<?php echo e($budget->description); ?>',
    amount: '<?php echo e($budget->amount); ?>',
    spent_amount: '<?php echo e($budget->spent_amount); ?>',
    allocated_date: '<?php echo e($budget->allocated_date ? $budget->allocated_date->format('Y-m-d') : ''); ?>',
    deadline: '<?php echo e($budget->deadline ? $budget->deadline->format('Y-m-d') : ''); ?>',
    notes: `<?php echo e($budget->notes); ?>`
};

// Format number input and update remaining amount
document.querySelectorAll('#amount, #spent_amount').forEach(input => {
    input.addEventListener('input', function() {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Update remaining amount display
        updateRemainingAmount();
    });
});

function updateRemainingAmount() {
    const amount = parseInt(document.getElementById('amount').value) || 0;
    const spentAmount = parseInt(document.getElementById('spent_amount').value) || 0;
    const remaining = amount - spentAmount;
    const percentage = amount > 0 ? ((remaining / amount) * 100) : 0;
    
    // Update display
    document.getElementById('remainingValue').textContent = remaining.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('remainingPercent').textContent = `(${percentage.toFixed(1)}%)`;
    
    // Update alert class based on remaining amount
    const alertDiv = document.getElementById('remainingAmount');
    alertDiv.className = 'alert';
    
    if (remaining < 0) {
        alertDiv.classList.add('alert-danger');
    } else if (percentage < 20) {
        alertDiv.classList.add('alert-warning');
    } else {
        alertDiv.classList.add('alert-info');
    }
    
    // Show warning if spent amount exceeds budget
    const spentInput = document.getElementById('spent_amount');
    if (spentAmount > amount && amount > 0) {
        spentInput.classList.add('is-invalid');
        if (!spentInput.nextElementSibling || !spentInput.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = 'Số tiền đã chi vượt quá ngân sách!';
            spentInput.parentNode.appendChild(feedback);
        }
    } else {
        spentInput.classList.remove('is-invalid');
        const feedback = spentInput.parentNode.querySelector('.invalid-feedback');
        if (feedback && feedback.textContent.includes('vượt quá ngân sách')) {
            feedback.remove();
        }
    }
}

// Reset form to original values
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn khôi phục về giá trị ban đầu?')) {
        Object.keys(originalValues).forEach(key => {
            const element = document.getElementById(key);
            if (element) {
                element.value = originalValues[key];
            }
        });
        
        // Trigger change events
        updateRemainingAmount();
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['event_id', 'category', 'description', 'amount'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ các trường bắt buộc!');
    }
});

// Initialize remaining amount calculation
updateRemainingAmount();

// Auto-save draft (optional feature)
let autoSaveTimer;
const formInputs = document.querySelectorAll('input, select, textarea');

formInputs.forEach(input => {
    input.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Auto-save logic could be implemented here
            console.log('Auto-saving draft...');
        }, 5000); // Save after 5 seconds of inactivity
    });
});

// Warn about unsaved changes
let formChanged = false;
formInputs.forEach(input => {
    input.addEventListener('change', function() {
        formChanged = true;
    });
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
    }
});

// Mark form as saved when submitted
document.querySelector('form').addEventListener('submit', function() {
    formChanged = false;
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\budgets\edit.blade.php ENDPATH**/ ?>