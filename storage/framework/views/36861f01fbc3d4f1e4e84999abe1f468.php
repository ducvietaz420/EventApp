<?php $__env->startSection('title', 'Thêm mốc thời gian'); ?>
<?php $__env->startSection('page-title', 'Thêm mốc thời gian'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('timelines.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Thông tin mốc thời gian</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('timelines.store')); ?>" method="POST" id="timelineForm">
                    <?php echo csrf_field(); ?>
                    
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
                                        <option value="<?php echo e($event->id); ?>" <?php echo e(old('event_id') == $event->id ? 'selected' : ''); ?>

                                                data-type="<?php echo e($event->type); ?>" data-date="<?php echo e($event->event_date); ?>">
                                            <?php echo e($event->name); ?> (<?php echo e($event->type_display); ?>)
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
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="title" name="title" value="<?php echo e(old('title')); ?>" required
                                       placeholder="Nhập tiêu đề mốc thời gian">
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
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  id="description" name="description" rows="3"
                                  placeholder="Mô tả chi tiết về mốc thời gian này"><?php echo e(old('description')); ?></textarea>
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
                                <label for="start_time" class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="start_time" name="start_time" value="<?php echo e(old('start_time')); ?>" required>
                                <?php $__errorArgs = ['start_time'];
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
                                <label for="end_time" class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="end_time" name="end_time" value="<?php echo e(old('end_time')); ?>" required>
                                <?php $__errorArgs = ['end_time'];
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
                                <label for="priority" class="form-label">Độ ưu tiên</label>
                                <select class="form-select <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="priority" name="priority">
                                    <option value="low" <?php echo e(old('priority', 'medium') === 'low' ? 'selected' : ''); ?>>Thấp</option>
                                    <option value="medium" <?php echo e(old('priority', 'medium') === 'medium' ? 'selected' : ''); ?>>Trung bình</option>
                                    <option value="high" <?php echo e(old('priority', 'medium') === 'high' ? 'selected' : ''); ?>>Cao</option>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Địa điểm</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="location" name="location" value="<?php echo e(old('location')); ?>"
                                       placeholder="Địa điểm thực hiện">
                                <?php $__errorArgs = ['location'];
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
                                <label for="responsible_person" class="form-label">Người phụ trách</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['responsible_person'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="responsible_person" name="responsible_person" value="<?php echo e(old('responsible_person')); ?>"
                                       placeholder="Tên người phụ trách">
                                <?php $__errorArgs = ['responsible_person'];
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
                                       id="contact_info" name="contact_info" value="<?php echo e(old('contact_info')); ?>"
                                       placeholder="Email hoặc số điện thoại">
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estimated_duration" class="form-label">Thời lượng dự kiến (phút)</label>
                                <input type="number" class="form-control <?php $__errorArgs = ['estimated_duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="estimated_duration" name="estimated_duration" value="<?php echo e(old('estimated_duration')); ?>" 
                                       min="1" placeholder="Ví dụ: 60">
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status" name="status">
                                    <option value="pending" <?php echo e(old('status', 'pending') === 'pending' ? 'selected' : ''); ?>>Đang chờ</option>
                                    <option value="in_progress" <?php echo e(old('status') === 'in_progress' ? 'selected' : ''); ?>>Đang thực hiện</option>
                                    <option value="completed" <?php echo e(old('status') === 'completed' ? 'selected' : ''); ?>>Hoàn thành</option>
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
                                  id="notes" name="notes" rows="3"
                                  placeholder="Ghi chú thêm về mốc thời gian này"><?php echo e(old('notes')); ?></textarea>
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
                    
                    <!-- Checkbox options -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_milestone" name="is_milestone" 
                                       value="1" <?php echo e(old('is_milestone') ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_milestone">
                                    Đây là mốc quan trọng
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" 
                                       value="1" <?php echo e(old('send_notification', true) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="send_notification">
                                    Gửi thông báo khi đến hạn
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="<?php echo e(route('timelines.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="previewTimeline()">
                                <i class="fas fa-eye me-2"></i>Xem trước
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Lưu mốc thời gian
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Gợi ý mốc thời gian -->
        <div class="card shadow mt-4" id="suggestionsCard" style="display: none;">
            <div class="card-header">
                <h6 class="mb-0">Gợi ý mốc thời gian cho loại sự kiện này</h6>
            </div>
            <div class="card-body">
                <div id="suggestionsList"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem trước -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem trước mốc thời gian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Lưu mốc thời gian</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Timeline suggestions based on event type
const timelineSuggestions = {
    'conference': [
        { title: 'Gửi thư mời diễn giả', days_before: 60, priority: 'high' },
        { title: 'Mở đăng ký tham dự', days_before: 45, priority: 'high' },
        { title: 'Chuẩn bị tài liệu hội thảo', days_before: 30, priority: 'medium' },
        { title: 'Kiểm tra thiết bị âm thanh', days_before: 7, priority: 'high' },
        { title: 'Setup phòng hội thảo', days_before: 1, priority: 'high' }
    ],
    'wedding': [
        { title: 'Đặt địa điểm tổ chức', days_before: 180, priority: 'high' },
        { title: 'Chọn thực đơn', days_before: 90, priority: 'high' },
        { title: 'Gửi thiệp mời', days_before: 60, priority: 'medium' },
        { title: 'Thử váy cưới lần cuối', days_before: 14, priority: 'high' },
        { title: 'Rehearsal dinner', days_before: 1, priority: 'medium' }
    ],
    'birthday': [
        { title: 'Lên kế hoạch tiệc', days_before: 30, priority: 'medium' },
        { title: 'Đặt bánh sinh nhật', days_before: 7, priority: 'high' },
        { title: 'Mua quà tặng', days_before: 5, priority: 'medium' },
        { title: 'Chuẩn bị trang trí', days_before: 1, priority: 'medium' }
    ],
    'meeting': [
        { title: 'Gửi agenda họp', days_before: 3, priority: 'high' },
        { title: 'Chuẩn bị tài liệu', days_before: 2, priority: 'medium' },
        { title: 'Kiểm tra phòng họp', days_before: 1, priority: 'medium' },
        { title: 'Setup thiết bị', days_before: 0, priority: 'high', hours_before: 1 }
    ],
    'training': [
        { title: 'Chuẩn bị tài liệu đào tạo', days_before: 14, priority: 'high' },
        { title: 'Gửi thông báo cho học viên', days_before: 7, priority: 'medium' },
        { title: 'Kiểm tra thiết bị', days_before: 1, priority: 'high' },
        { title: 'Setup phòng đào tạo', days_before: 0, priority: 'high', hours_before: 2 }
    ]
};

// Event selection change handler
document.getElementById('event_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const eventType = selectedOption.dataset.type;
        const eventDate = selectedOption.dataset.date;
        
        // Auto-fill location if event has location
        // This would need to be implemented with AJAX to get event details
        
        // Show suggestions
        showSuggestions(eventType, eventDate);
        
        // Auto-suggest due date based on event date
        if (eventDate) {
            const eventDateObj = new Date(eventDate);
            const suggestedDate = new Date(eventDateObj);
            suggestedDate.setDate(suggestedDate.getDate() - 7); // 1 week before event
            
            document.getElementById('due_date').value = suggestedDate.toISOString().split('T')[0];
        }
    } else {
        hideSuggestions();
    }
});

function showSuggestions(eventType, eventDate) {
    const suggestions = timelineSuggestions[eventType];
    if (!suggestions || !eventDate) {
        hideSuggestions();
        return;
    }
    
    const eventDateObj = new Date(eventDate);
    const suggestionsList = document.getElementById('suggestionsList');
    
    let html = '<div class="row">';
    suggestions.forEach((suggestion, index) => {
        const dueDate = new Date(eventDateObj);
        dueDate.setDate(dueDate.getDate() - suggestion.days_before);
        
        const priorityClass = {
            'high': 'danger',
            'medium': 'warning', 
            'low': 'secondary'
        }[suggestion.priority];
        
        html += `
            <div class="col-md-6 mb-2">
                <div class="card border-${priorityClass}">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="fw-bold">${suggestion.title}</small><br>
                                <small class="text-muted">${dueDate.toLocaleDateString('vi-VN')}</small>
                                <span class="badge bg-${priorityClass} ms-1">${suggestion.priority}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="applySuggestion(${index}, '${eventType}')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    suggestionsList.innerHTML = html;
    document.getElementById('suggestionsCard').style.display = 'block';
}

function hideSuggestions() {
    document.getElementById('suggestionsCard').style.display = 'none';
}

function applySuggestion(index, eventType) {
    const suggestion = timelineSuggestions[eventType][index];
    const eventDate = document.querySelector('#event_id option:checked').dataset.date;
    
    if (!eventDate) return;
    
    const eventDateObj = new Date(eventDate);
    const dueDate = new Date(eventDateObj);
    dueDate.setDate(dueDate.getDate() - suggestion.days_before);
    
    // Fill form fields
    document.getElementById('title').value = suggestion.title;
    document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];
    document.getElementById('priority').value = suggestion.priority;
    
    if (suggestion.hours_before) {
        const dueTime = new Date(eventDateObj);
        dueTime.setHours(dueTime.getHours() - suggestion.hours_before);
        document.getElementById('due_time').value = dueTime.toTimeString().slice(0, 5);
    }
    
    // Scroll to form
    document.getElementById('timelineForm').scrollIntoView({ behavior: 'smooth' });
}

// Auto-generate title based on event and description
document.getElementById('description').addEventListener('blur', function() {
    const title = document.getElementById('title');
    if (!title.value && this.value) {
        const eventName = document.querySelector('#event_id option:checked').text;
        if (eventName && eventName !== 'Chọn sự kiện') {
            title.value = `${this.value} - ${eventName.split(' (')[0]}`;
        }
    }
});

// Form validation
document.getElementById('timelineForm').addEventListener('submit', function(e) {
    const requiredFields = ['event_id', 'title', 'due_date'];
    let isValid = true;
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Validate due date is not in the past (unless status is completed)
    const dueDate = new Date(document.getElementById('due_date').value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (dueDate < today && document.getElementById('status').value !== 'completed') {
        if (!confirm('Ngày thực hiện đã qua. Bạn có chắc chắn muốn tiếp tục?')) {
            e.preventDefault();
            return;
        }
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ các trường bắt buộc!');
    }
});

// Preview timeline
function previewTimeline() {
    const formData = new FormData(document.getElementById('timelineForm'));
    const data = Object.fromEntries(formData.entries());
    
    const eventName = document.querySelector('#event_id option:checked').text;
    const priorityText = {
        'low': 'Thấp',
        'medium': 'Trung bình',
        'high': 'Cao'
    }[data.priority] || 'Trung bình';
    
    const statusText = {
        'pending': 'Đang chờ',
        'in_progress': 'Đang thực hiện',
        'completed': 'Hoàn thành'
    }[data.status] || 'Đang chờ';
    
    let html = `
        <div class="timeline-preview">
            <h6 class="fw-bold">${data.title || 'Chưa có tiêu đề'}</h6>
            <p class="text-muted mb-2">${data.description || 'Chưa có mô tả'}</p>
            
            <div class="row">
                <div class="col-md-6">
                    <strong>Sự kiện:</strong> ${eventName !== 'Chọn sự kiện' ? eventName : 'Chưa chọn'}<br>
                    <strong>Ngày:</strong> ${data.due_date ? new Date(data.due_date).toLocaleDateString('vi-VN') : 'Chưa xác định'}<br>
                    <strong>Thời gian:</strong> ${data.due_time || 'Cả ngày'}<br>
                    <strong>Thời lượng:</strong> ${data.duration ? data.duration + ' phút' : 'Chưa xác định'}
                </div>
                <div class="col-md-6">
                    <strong>Độ ưu tiên:</strong> <span class="badge bg-${
                        data.priority === 'high' ? 'danger' : 
                        data.priority === 'medium' ? 'warning' : 'secondary'
                    }">${priorityText}</span><br>
                    <strong>Trạng thái:</strong> <span class="badge bg-${
                        data.status === 'completed' ? 'success' : 
                        data.status === 'in_progress' ? 'primary' : 'warning'
                    }">${statusText}</span><br>
                    <strong>Địa điểm:</strong> ${data.location || 'Chưa xác định'}<br>
                    <strong>Người phụ trách:</strong> ${data.assigned_to || 'Chưa xác định'}
                </div>
            </div>
            
            ${data.notes ? `<div class="mt-3"><strong>Ghi chú:</strong><br>${data.notes}</div>` : ''}
            
            <div class="mt-3">
                ${data.is_milestone ? '<span class="badge bg-info me-2">Mốc quan trọng</span>' : ''}
                ${data.send_notification ? '<span class="badge bg-success me-2">Có thông báo</span>' : ''}
                ${data.reminder_before ? `<span class="badge bg-warning">Nhắc nhở trước ${data.reminder_before} phút</span>` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

function submitForm() {
    document.getElementById('timelineForm').submit();
}

// Auto-save draft (optional)
let autoSaveTimer;
function autoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        const formData = new FormData(document.getElementById('timelineForm'));
        const data = Object.fromEntries(formData.entries());
        localStorage.setItem('timeline_draft', JSON.stringify(data));
    }, 2000);
}

// Load draft on page load
window.addEventListener('load', function() {
    const draft = localStorage.getItem('timeline_draft');
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const field = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = data[key] === '1';
                } else {
                    field.value = data[key];
                }
            }
        });
    }
});

// Add auto-save listeners
document.querySelectorAll('#timelineForm input, #timelineForm select, #timelineForm textarea').forEach(field => {
    field.addEventListener('input', autoSave);
    field.addEventListener('change', autoSave);
});

// Clear draft on successful submit
document.getElementById('timelineForm').addEventListener('submit', function() {
    localStorage.removeItem('timeline_draft');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\timelines\create.blade.php ENDPATH**/ ?>