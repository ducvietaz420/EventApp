<?php $__env->startSection('title', 'Thêm công việc mới'); ?>
<?php $__env->startSection('page-title', 'Thêm công việc mới'); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="btn-group" role="group">
        <a href="<?php echo e(route('checklists.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <button type="button" class="btn btn-info" onclick="previewChecklist()">
            <i class="fas fa-eye me-2"></i>Xem trước
        </button>
        <button type="button" class="btn btn-warning" onclick="saveDraft()">
            <i class="fas fa-save me-2"></i>Lưu nháp
        </button>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <!-- Form tạo công việc -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Thông tin công việc</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('checklists.store')); ?>" id="checklistForm">
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
unset($__errorArgs, $__bag); ?>" 
                                        id="event_id" name="event_id" required onchange="loadChecklistSuggestions()">
                                    <option value="">Chọn sự kiện</option>
                                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($event->id); ?>" <?php echo e(old('event_id') == $event->id ? 'selected' : ''); ?>

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
                                       id="title" name="title" value="<?php echo e(old('title')); ?>" required 
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
                                  placeholder="Mô tả chi tiết về công việc..."><?php echo e(old('description')); ?></textarea>
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
                                       id="due_date" name="due_date" value="<?php echo e(old('due_date')); ?>">
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
                                       id="due_time" name="due_time" value="<?php echo e(old('due_time')); ?>">
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
                                       id="assigned_to" name="assigned_to" value="<?php echo e(old('assigned_to')); ?>" 
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
                                       id="contact_info" name="contact_info" value="<?php echo e(old('contact_info')); ?>" 
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
                        <div class="col-md-6">
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
                                       id="estimated_duration" name="estimated_duration" value="<?php echo e(old('estimated_duration')); ?>" 
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
                        <div class="col-md-6">
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
                                    <option value="15" <?php echo e(old('reminder_before') == '15' ? 'selected' : ''); ?>>15 phút</option>
                                    <option value="30" <?php echo e(old('reminder_before') == '30' ? 'selected' : ''); ?>>30 phút</option>
                                    <option value="60" <?php echo e(old('reminder_before') == '60' ? 'selected' : ''); ?>>1 giờ</option>
                                    <option value="120" <?php echo e(old('reminder_before') == '120' ? 'selected' : ''); ?>>2 giờ</option>
                                    <option value="1440" <?php echo e(old('reminder_before') == '1440' ? 'selected' : ''); ?>>1 ngày</option>
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
                                  placeholder="Ghi chú thêm về công việc..."><?php echo e(old('notes')); ?></textarea>
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
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_important" 
                                       name="is_important" value="1" <?php echo e(old('is_important') ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_important">
                                    <i class="fas fa-star text-warning me-1"></i>Công việc quan trọng
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="send_notification" 
                                       name="send_notification" value="1" <?php echo e(old('send_notification', true) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="send_notification">
                                    <i class="fas fa-bell text-info me-1"></i>Gửi thông báo
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo e(route('checklists.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Hủy
                        </a>
                        <button type="button" class="btn btn-info" onclick="previewChecklist()">
                            <i class="fas fa-eye me-2"></i>Xem trước
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Lưu công việc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Gợi ý công việc -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">Gợi ý công việc</h6>
            </div>
            <div class="card-body">
                <div id="checklistSuggestions">
                    <p class="text-muted text-center">Chọn sự kiện để xem gợi ý công việc</p>
                </div>
            </div>
        </div>
        
        <!-- Hướng dẫn -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="mb-0">Hướng dẫn</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <h6>Mẹo tạo công việc hiệu quả:</h6>
                    <ul class="mb-3">
                        <li>Đặt tên công việc rõ ràng, cụ thể</li>
                        <li>Mô tả chi tiết những gì cần làm</li>
                        <li>Đặt thời hạn hợp lý</li>
                        <li>Gán người phụ trách cụ thể</li>
                        <li>Ước tính thời gian thực hiện</li>
                    </ul>
                    
                    <h6>Độ ưu tiên:</h6>
                    <ul class="mb-3">
                        <li><span class="badge bg-danger">Cao</span> - Cần hoàn thành ngay</li>
                        <li><span class="badge bg-warning">Trung bình</span> - Quan trọng nhưng không gấp</li>
                        <li><span class="badge bg-secondary">Thấp</span> - Có thể hoãn lại</li>
                    </ul>
                    
                    <h6>Nhắc nhở:</h6>
                    <p>Thiết lập nhắc nhở để không bỏ lỡ công việc quan trọng.</p>
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
                <button type="button" class="btn btn-primary" onclick="submitForm()">Lưu công việc</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Checklist suggestions data
const checklistSuggestions = {
    'wedding': [
        { title: 'Đặt địa điểm tổ chức', description: 'Liên hệ và đặt trước địa điểm tổ chức tiệc cưới', priority: 'high', duration: 120 },
        { title: 'Chọn thực đơn', description: 'Thảo luận và quyết định thực đơn cho tiệc cưới', priority: 'high', duration: 90 },
        { title: 'Thuê nhiếp ảnh gia', description: 'Tìm và thuê nhiếp ảnh gia chụp ảnh cưới', priority: 'high', duration: 60 },
        { title: 'Đặt hoa trang trí', description: 'Chọn và đặt hoa trang trí cho lễ cưới', priority: 'medium', duration: 45 },
        { title: 'Gửi thiệp mời', description: 'In và gửi thiệp mời cho khách mời', priority: 'medium', duration: 180 },
        { title: 'Thuê ban nhạc', description: 'Liên hệ và thuê ban nhạc hoặc DJ', priority: 'medium', duration: 60 },
        { title: 'Chuẩn bị trang phục', description: 'Thử và hoàn thiện trang phục cô dâu chú rể', priority: 'high', duration: 120 }
    ],
    'conference': [
        { title: 'Đặt hội trường', description: 'Liên hệ và đặt hội trường phù hợp', priority: 'high', duration: 60 },
        { title: 'Mời diễn giả', description: 'Liên hệ và mời các diễn giả tham gia', priority: 'high', duration: 120 },
        { title: 'Chuẩn bị thiết bị', description: 'Kiểm tra và chuẩn bị thiết bị âm thanh, ánh sáng', priority: 'high', duration: 90 },
        { title: 'Đăng ký tham dự', description: 'Mở đăng ký và quản lý danh sách tham dự', priority: 'medium', duration: 30 },
        { title: 'Chuẩn bị tài liệu', description: 'In ấn và chuẩn bị tài liệu hội thảo', priority: 'medium', duration: 60 },
        { title: 'Đặt catering', description: 'Đặt dịch vụ ăn uống cho giờ nghỉ', priority: 'medium', duration: 45 }
    ],
    'birthday': [
        { title: 'Đặt bánh sinh nhật', description: 'Đặt bánh sinh nhật theo yêu cầu', priority: 'high', duration: 30 },
        { title: 'Trang trí không gian', description: 'Mua và trang trí bóng bay, banner', priority: 'medium', duration: 90 },
        { title: 'Mời khách', description: 'Gọi điện hoặc nhắn tin mời khách tham dự', priority: 'medium', duration: 60 },
        { title: 'Chuẩn bị quà tặng', description: 'Mua và gói quà tặng sinh nhật', priority: 'medium', duration: 45 },
        { title: 'Chuẩn bị âm nhạc', description: 'Tạo playlist nhạc cho bữa tiệc', priority: 'low', duration: 30 },
        { title: 'Dọn dẹp sau tiệc', description: 'Dọn dẹp và thu dọn sau khi tiệc kết thúc', priority: 'low', duration: 60 }
    ],
    'meeting': [
        { title: 'Chuẩn bị agenda', description: 'Soạn thảo chương trình họp chi tiết', priority: 'high', duration: 45 },
        { title: 'Đặt phòng họp', description: 'Đặt và kiểm tra phòng họp', priority: 'high', duration: 15 },
        { title: 'Gửi lời mời', description: 'Gửi lời mời họp cho các thành viên', priority: 'medium', duration: 20 },
        { title: 'Chuẩn bị tài liệu', description: 'In và chuẩn bị tài liệu họp', priority: 'medium', duration: 30 },
        { title: 'Kiểm tra thiết bị', description: 'Kiểm tra máy chiếu, micro, laptop', priority: 'medium', duration: 15 },
        { title: 'Ghi chú biên bản', description: 'Ghi chép biên bản cuộc họp', priority: 'high', duration: 60 }
    ]
};

// Load checklist suggestions based on event type
function loadChecklistSuggestions() {
    const eventSelect = document.getElementById('event_id');
    const selectedOption = eventSelect.options[eventSelect.selectedIndex];
    const eventType = selectedOption.getAttribute('data-type');
    const eventDate = selectedOption.getAttribute('data-date');
    
    const suggestionsContainer = document.getElementById('checklistSuggestions');
    
    if (!eventType || !checklistSuggestions[eventType]) {
        suggestionsContainer.innerHTML = '<p class="text-muted text-center">Không có gợi ý cho loại sự kiện này</p>';
        return;
    }
    
    const suggestions = checklistSuggestions[eventType];
    let html = '<h6 class="mb-3">Gợi ý cho ' + selectedOption.text + ':</h6>';
    
    suggestions.forEach((suggestion, index) => {
        const priorityClass = suggestion.priority === 'high' ? 'danger' : 
                             suggestion.priority === 'medium' ? 'warning' : 'secondary';
        
        html += `
            <div class="border rounded p-2 mb-2 suggestion-item" style="cursor: pointer;" 
                 onclick="applySuggestion(${index}, '${eventType}')">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${suggestion.title}</h6>
                        <p class="mb-1 small text-muted">${suggestion.description}</p>
                        <div>
                            <span class="badge bg-${priorityClass} me-1">${suggestion.priority}</span>
                            <small class="text-muted">${suggestion.duration} phút</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="event.stopPropagation(); applySuggestion(${index}, '${eventType}')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    suggestionsContainer.innerHTML = html;
    
    // Auto-suggest due date based on event date
    if (eventDate) {
        const eventDateObj = new Date(eventDate);
        const suggestedDate = new Date(eventDateObj.getTime() - (7 * 24 * 60 * 60 * 1000)); // 1 week before
        document.getElementById('due_date').value = suggestedDate.toISOString().split('T')[0];
    }
}

// Apply suggestion to form
function applySuggestion(index, eventType) {
    const suggestion = checklistSuggestions[eventType][index];
    
    document.getElementById('title').value = suggestion.title;
    document.getElementById('description').value = suggestion.description;
    document.getElementById('priority').value = suggestion.priority;
    document.getElementById('estimated_duration').value = suggestion.duration;
    
    // Show success message
    showToast('Đã áp dụng gợi ý: ' + suggestion.title, 'success');
}

// Auto-fill title based on common patterns
document.getElementById('title').addEventListener('input', function() {
    const title = this.value.toLowerCase();
    const descriptionField = document.getElementById('description');
    
    if (title.includes('đặt') && title.includes('địa điểm')) {
        if (!descriptionField.value) {
            descriptionField.value = 'Liên hệ và đặt trước địa điểm tổ chức sự kiện';
        }
    } else if (title.includes('mời') && title.includes('khách')) {
        if (!descriptionField.value) {
            descriptionField.value = 'Gửi lời mời và xác nhận danh sách khách tham dự';
        }
    } else if (title.includes('chuẩn bị') && title.includes('thiết bị')) {
        if (!descriptionField.value) {
            descriptionField.value = 'Kiểm tra và chuẩn bị đầy đủ thiết bị cần thiết';
        }
    }
});

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
    if (data.send_notification) flags.push('<span class="badge bg-info"><i class="fas fa-bell"></i> Thông báo</span>');
    
    if (flags.length > 0) {
        html += `
            <div class="mt-3">
                <h6>Đặc điểm</h6>
                <div>${flags.join(' ')}</div>
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

// Auto-save draft to localStorage
function saveDraft() {
    const formData = new FormData(document.getElementById('checklistForm'));
    const data = Object.fromEntries(formData.entries());
    
    localStorage.setItem('checklistDraft', JSON.stringify(data));
    showToast('Đã lưu bản nháp!', 'success');
}

// Load draft from localStorage
function loadDraft() {
    const draft = localStorage.getItem('checklistDraft');
    if (draft) {
        const data = JSON.parse(draft);
        
        Object.keys(data).forEach(key => {
            const element = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = data[key] === '1';
                } else {
                    element.value = data[key];
                }
            }
        });
        
        showToast('Đã tải bản nháp!', 'info');
    }
}

// Auto-save every 30 seconds
setInterval(function() {
    const title = document.getElementById('title').value;
    if (title.trim()) {
        saveDraft();
    }
}, 30000);

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a draft and ask user if they want to load it
    const draft = localStorage.getItem('checklistDraft');
    if (draft && confirm('Bạn có muốn tải bản nháp đã lưu?')) {
        loadDraft();
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\checklists\create.blade.php ENDPATH**/ ?>