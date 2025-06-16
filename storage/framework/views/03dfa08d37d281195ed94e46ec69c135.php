<?php $__env->startSection('title', 'Chỉnh sửa công việc'); ?>
<?php $__env->startSection('page-title', 'Chỉnh sửa công việc: ' . $checklist->title); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="btn-group" role="group">
        <a href="<?php echo e(route('checklists.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
        <a href="<?php echo e(route('checklists.show', $checklist)); ?>" class="btn btn-outline-info">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
        <button type="button" class="btn btn-info" onclick="previewChecklist()">
            <i class="fas fa-eye me-2"></i>Xem trước
        </button>
        <button type="button" class="btn btn-warning" onclick="resetForm()">
            <i class="fas fa-undo me-2"></i>Khôi phục
        </button>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <!-- Form chỉnh sửa công việc -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Thông tin công việc</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('checklists.update', $checklist)); ?>" id="checklistForm">
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
unset($__errorArgs, $__bag); ?>" 
                                        id="event_id" name="event_id" required>
                                    <option value="">Chọn sự kiện</option>
                                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($event->id); ?>" 
                                                <?php echo e(old('event_id', $checklist->event_id) == $event->id ? 'selected' : ''); ?>

                                                data-type="<?php echo e($event->type); ?>" data-date="<?php echo e($event->event_date); ?>">
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
                                <label for="title" class="form-label">Tên công việc <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="title" name="title" value="<?php echo e(old('title', $checklist->title)); ?>" required 
                                       placeholder="Nhập tên công việc...">
                                <?php $__errorArgs = ['title'];
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
                        <label for="description" class="form-label">Mô tả công việc</label>
                        <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Mô tả chi tiết về công việc..."><?php echo e(old('description', $checklist->description)); ?></textarea>
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Ngày hạn</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="due_date" name="due_date" 
                                       value="<?php echo e(old('due_date', $checklist->due_date ? $checklist->due_date->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['due_date'];
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="due_time" class="form-label">Giờ hạn</label>
                                <input type="time" class="form-control <?php $__errorArgs = ['due_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="due_time" name="due_time" 
                                       value="<?php echo e(old('due_time', $checklist->due_time ? $checklist->due_time->format('H:i') : '')); ?>">
                                <?php $__errorArgs = ['due_time'];
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Độ ưu tiên</label>
                                <select class="form-select <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="priority" name="priority">
                                    <option value="low" <?php echo e(old('priority', $checklist->priority) === 'low' ? 'selected' : ''); ?>>Thấp</option>
                                    <option value="medium" <?php echo e(old('priority', $checklist->priority) === 'medium' ? 'selected' : ''); ?>>Trung bình</option>
                                    <option value="high" <?php echo e(old('priority', $checklist->priority) === 'high' ? 'selected' : ''); ?>>Cao</option>
                                </select>
                                <?php $__errorArgs = ['priority'];
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Người phụ trách</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="assigned_to" name="assigned_to" 
                                       value="<?php echo e(old('assigned_to', $checklist->assigned_to)); ?>" 
                                       placeholder="Nhập tên người phụ trách...">
                                <?php $__errorArgs = ['assigned_to'];
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
                                <label for="contact_info" class="form-label">Thông tin liên hệ</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['contact_info'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="contact_info" name="contact_info" 
                                       value="<?php echo e(old('contact_info', $checklist->contact_info)); ?>" 
                                       placeholder="Email, số điện thoại...">
                                <?php $__errorArgs = ['contact_info'];
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
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estimated_duration" class="form-label">Thời gian ước tính (phút)</label>
                                <input type="number" class="form-control <?php $__errorArgs = ['estimated_duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="estimated_duration" name="estimated_duration" 
                                       value="<?php echo e(old('estimated_duration', $checklist->estimated_duration)); ?>" 
                                       min="1" placeholder="60">
                                <?php $__errorArgs = ['estimated_duration'];
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="reminder_before" class="form-label">Nhắc nhở trước (phút)</label>
                                <select class="form-select <?php $__errorArgs = ['reminder_before'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="reminder_before" name="reminder_before">
                                    <option value="">Không nhắc nhở</option>
                                    <option value="15" <?php echo e(old('reminder_before', $checklist->reminder_before) == '15' ? 'selected' : ''); ?>>15 phút</option>
                                    <option value="30" <?php echo e(old('reminder_before', $checklist->reminder_before) == '30' ? 'selected' : ''); ?>>30 phút</option>
                                    <option value="60" <?php echo e(old('reminder_before', $checklist->reminder_before) == '60' ? 'selected' : ''); ?>>1 giờ</option>
                                    <option value="120" <?php echo e(old('reminder_before', $checklist->reminder_before) == '120' ? 'selected' : ''); ?>>2 giờ</option>
                                    <option value="1440" <?php echo e(old('reminder_before', $checklist->reminder_before) == '1440' ? 'selected' : ''); ?>>1 ngày</option>
                                </select>
                                <?php $__errorArgs = ['reminder_before'];
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="status" name="status">
                                    <option value="pending" <?php echo e(old('status', $checklist->status ?? 'pending') === 'pending' ? 'selected' : ''); ?>>Đang chờ</option>
                                    <option value="in_progress" <?php echo e(old('status', $checklist->status ?? 'pending') === 'in_progress' ? 'selected' : ''); ?>>Đang thực hiện</option>
                                    <option value="completed" <?php echo e(old('status', $checklist->status ?? 'pending') === 'completed' ? 'selected' : ''); ?>>Hoàn thành</option>
                                </select>
                                <?php $__errorArgs = ['status'];
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
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  id="notes" name="notes" rows="2" 
                                  placeholder="Ghi chú thêm về công việc..."><?php echo e(old('notes', $checklist->notes)); ?></textarea>
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
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_important" 
                                       name="is_important" value="1" 
                                       <?php echo e(old('is_important', $checklist->is_important) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_important">
                                    <i class="fas fa-star text-warning me-1"></i>Công việc quan trọng
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_completed" 
                                       name="is_completed" value="1" 
                                       <?php echo e(old('is_completed', $checklist->is_completed) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_completed">
                                    <i class="fas fa-check-circle text-success me-1"></i>Đã hoàn thành
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="send_notification" 
                                       name="send_notification" value="1" 
                                       <?php echo e(old('send_notification', $checklist->send_notification ?? true) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="send_notification">
                                    <i class="fas fa-bell text-info me-1"></i>Gửi thông báo
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo e(route('checklists.show', $checklist)); ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button type="button" class="btn btn-warning" onclick="resetForm()">
                            <i class="fas fa-undo me-2"></i>Khôi phục
                        </button>
                        <button type="button" class="btn btn-info" onclick="previewChecklist()">
                            <i class="fas fa-eye me-2"></i>Xem trước
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật công việc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Thông tin nhanh -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Thông tin nhanh</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="mb-0">
                                <span class="badge bg-<?php echo e($checklist->priority === 'high' ? 'danger' : ($checklist->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                    <?php echo e(ucfirst($checklist->priority)); ?>

                                </span>
                            </h5>
                            <small class="text-muted">Độ ưu tiên</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0">
                            <?php if($checklist->is_completed): ?>
                                <span class="badge bg-success">Hoàn thành</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Đang chờ</span>
                            <?php endif; ?>
                        </h5>
                        <small class="text-muted">Trạng thái</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-calendar text-muted me-1"></i>Sự kiện:</span>
                        <span><?php echo e($checklist->event->name); ?></span>
                    </div>
                    
                    <?php if($checklist->due_date): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-clock text-muted me-1"></i>Hạn:</span>
                            <span>
                                <?php echo e($checklist->due_date->format('d/m/Y')); ?>

                                <?php if($checklist->due_time): ?>
                                    <?php echo e($checklist->due_time->format('H:i')); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($checklist->assigned_to): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-user text-muted me-1"></i>Phụ trách:</span>
                            <span><?php echo e($checklist->assigned_to); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($checklist->estimated_duration): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-hourglass text-muted me-1"></i>Ước tính:</span>
                            <span><?php echo e($checklist->estimated_duration); ?> phút</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Lịch sử thay đổi -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Lịch sử thay đổi</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Tạo công việc</h6>
                            <p class="mb-0 small text-muted">
                                <?php echo e($checklist->created_at->format('d/m/Y H:i')); ?>

                            </p>
                        </div>
                    </div>
                    
                    <?php if($checklist->updated_at != $checklist->created_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Cập nhật cuối</h6>
                                <p class="mb-0 small text-muted">
                                    <?php echo e($checklist->updated_at->format('d/m/Y H:i')); ?>

                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($checklist->is_completed && $checklist->completed_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Hoàn thành</h6>
                                <p class="mb-0 small text-muted">
                                    <?php echo e($checklist->completed_at->format('d/m/Y H:i')); ?>

                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem trước -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem trước công việc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Cập nhật công việc</button>
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
    padding: 10px 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Store original form data
let originalFormData = {};

// Capture original form data on page load
document.addEventListener('DOMContentLoaded', function() {
    captureOriginalData();
    
    // Track form changes
    const form = document.getElementById('checklistForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', trackChanges);
        input.addEventListener('input', trackChanges);
    });
    
    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = 'Bạn có thay đổi chưa được lưu. Bạn có chắc chắn muốn rời khỏi trang?';
        }
    });
});

// Capture original form data
function captureOriginalData() {
    const formData = new FormData(document.getElementById('checklistForm'));
    originalFormData = Object.fromEntries(formData.entries());
    
    // Handle checkboxes separately
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        originalFormData[checkbox.name] = checkbox.checked ? '1' : '0';
    });
}

// Track form changes
function trackChanges() {
    const hasChanges = hasUnsavedChanges();
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (hasChanges) {
        submitBtn.classList.add('btn-warning');
        submitBtn.classList.remove('btn-primary');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Lưu thay đổi';
    } else {
        submitBtn.classList.add('btn-primary');
        submitBtn.classList.remove('btn-warning');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Cập nhật công việc';
    }
}

// Check if there are unsaved changes
function hasUnsavedChanges() {
    const currentFormData = new FormData(document.getElementById('checklistForm'));
    const currentData = Object.fromEntries(currentFormData.entries());
    
    // Handle checkboxes
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        currentData[checkbox.name] = checkbox.checked ? '1' : '0';
    });
    
    // Compare with original data
    for (const key in originalFormData) {
        if (originalFormData[key] !== (currentData[key] || '')) {
            return true;
        }
    }
    
    for (const key in currentData) {
        if ((originalFormData[key] || '') !== currentData[key]) {
            return true;
        }
    }
    
    return false;
}

// Reset form to original values
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn khôi phục về giá trị ban đầu?')) {
        Object.keys(originalFormData).forEach(key => {
            const element = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = originalFormData[key] === '1';
                } else {
                    element.value = originalFormData[key] || '';
                }
            }
        });
        
        trackChanges();
        showToast('Đã khôi phục về giá trị ban đầu!', 'success');
    }
}

// Preview checklist
function previewChecklist() {
    const formData = new FormData(document.getElementById('checklistForm'));
    const data = Object.fromEntries(formData.entries());
    
    // Get event name
    const eventSelect = document.getElementById('event_id');
    const eventName = eventSelect.options[eventSelect.selectedIndex].text;
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Thông tin cơ bản</h6>
                <table class="table table-sm">
                    <tr><td><strong>Sự kiện:</strong></td><td>${eventName}</td></tr>
                    <tr><td><strong>Tên công việc:</strong></td><td>${data.title || 'Chưa nhập'}</td></tr>
                    <tr><td><strong>Độ ưu tiên:</strong></td><td>
                        <span class="badge bg-${data.priority === 'high' ? 'danger' : data.priority === 'medium' ? 'warning' : 'secondary'}">
                            ${data.priority === 'high' ? 'Cao' : data.priority === 'medium' ? 'Trung bình' : 'Thấp'}
                        </span>
                    </td></tr>
                    <tr><td><strong>Trạng thái:</strong></td><td>
                        <span class="badge bg-${data.status === 'completed' ? 'success' : data.status === 'in_progress' ? 'info' : 'warning'}">
                            ${data.status === 'completed' ? 'Hoàn thành' : data.status === 'in_progress' ? 'Đang thực hiện' : 'Đang chờ'}
                        </span>
                    </td></tr>
                    <tr><td><strong>Người phụ trách:</strong></td><td>${data.assigned_to || 'Chưa gán'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Thời gian</h6>
                <table class="table table-sm">
                    <tr><td><strong>Ngày hạn:</strong></td><td>${data.due_date ? new Date(data.due_date).toLocaleDateString('vi-VN') : 'Chưa đặt'}</td></tr>
                    <tr><td><strong>Giờ hạn:</strong></td><td>${data.due_time || 'Chưa đặt'}</td></tr>
                    <tr><td><strong>Thời gian ước tính:</strong></td><td>${data.estimated_duration ? data.estimated_duration + ' phút' : 'Chưa ước tính'}</td></tr>
                    <tr><td><strong>Nhắc nhở trước:</strong></td><td>${data.reminder_before ? data.reminder_before + ' phút' : 'Không'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    if (data.description) {
        html += `
            <div class="mt-3">
                <h6>Mô tả</h6>
                <p class="border rounded p-2">${data.description}</p>
            </div>
        `;
    }
    
    if (data.notes) {
        html += `
            <div class="mt-3">
                <h6>Ghi chú</h6>
                <p class="border rounded p-2">${data.notes}</p>
            </div>
        `;
    }
    
    if (data.contact_info) {
        html += `
            <div class="mt-3">
                <h6>Thông tin liên hệ</h6>
                <p class="border rounded p-2">${data.contact_info}</p>
            </div>
        `;
    }
    
    const flags = [];
    if (data.is_important) flags.push('<span class="badge bg-warning"><i class="fas fa-star"></i> Quan trọng</span>');
    if (data.is_completed) flags.push('<span class="badge bg-success"><i class="fas fa-check-circle"></i> Hoàn thành</span>');
    if (data.send_notification) flags.push('<span class="badge bg-info"><i class="fas fa-bell"></i> Thông báo</span>');
    
    if (flags.length > 0) {
        html += `
            <div class="mt-3">
                <h6>Đặc điểm</h6>
                <div>${flags.join(' ')}</div>
            </div>
        `;
    }
    
    // Show changes
    if (hasUnsavedChanges()) {
        html += `
            <div class="mt-3">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Có thay đổi chưa được lưu!</strong>
                </div>
            </div>
        `;
    }
    
    document.getElementById('previewContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

// Submit form
function submitForm() {
    document.getElementById('checklistForm').submit();
}

// Form validation
document.getElementById('checklistForm').addEventListener('submit', function(e) {
    const eventId = document.getElementById('event_id').value;
    const title = document.getElementById('title').value.trim();
    const dueDate = document.getElementById('due_date').value;
    
    if (!eventId) {
        e.preventDefault();
        showToast('Vui lòng chọn sự kiện!', 'error');
        document.getElementById('event_id').focus();
        return;
    }
    
    if (!title) {
        e.preventDefault();
        showToast('Vui lòng nhập tên công việc!', 'error');
        document.getElementById('title').focus();
        return;
    }
    
    if (dueDate) {
        const today = new Date();
        const selectedDate = new Date(dueDate);
        
        if (selectedDate < today.setHours(0,0,0,0)) {
            if (!confirm('Ngày hạn đã qua. Bạn có chắc chắn muốn tiếp tục?')) {
                e.preventDefault();
                return;
            }
        }
    }
});

// Auto-suggest due date when event changes
document.getElementById('event_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const eventDate = selectedOption.getAttribute('data-date');
    
    if (eventDate && !document.getElementById('due_date').value) {
        const eventDateObj = new Date(eventDate);
        const suggestedDate = new Date(eventDateObj.getTime() - (7 * 24 * 60 * 60 * 1000)); // 1 week before
        document.getElementById('due_date').value = suggestedDate.toISOString().split('T')[0];
        
        showToast('Đã gợi ý ngày hạn dựa trên ngày sự kiện', 'info');
    }
});

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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\checklists\edit.blade.php ENDPATH**/ ?>