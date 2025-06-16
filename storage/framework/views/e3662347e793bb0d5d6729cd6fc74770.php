

<?php $__env->startSection('title', 'Chi tiết gợi ý AI'); ?>

<?php $__env->startSection('page-title', 'Chi tiết gợi ý AI'); ?>

<?php $__env->startSection('page-actions'); ?>
    <div class="btn-group" role="group">
        <a href="<?php echo e(route('ai-suggestions.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
        <?php if($aiSuggestion->status === 'generated'): ?>
            <button type="button" class="btn btn-success" onclick="updateStatus(<?php echo e($aiSuggestion->id); ?>, 'accepted')">
                <i class="fas fa-check me-2"></i>Chấp nhận
            </button>
            <button type="button" class="btn btn-danger" onclick="updateStatus(<?php echo e($aiSuggestion->id); ?>, 'rejected')">
                <i class="fas fa-times me-2"></i>Từ chối
            </button>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-warning" onclick="toggleFavorite(<?php echo e($aiSuggestion->id); ?>)">
            <i class="fas fa-star<?php echo e($aiSuggestion->is_favorite ? '' : '-o'); ?> me-2"></i>
            <?php echo e($aiSuggestion->is_favorite ? 'Bỏ yêu thích' : 'Yêu thích'); ?>

        </button>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2"><?php echo e($aiSuggestion->title); ?></h1>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary"><?php echo e(ucfirst($aiSuggestion->suggestion_type)); ?></span>
                        <span class="badge bg-<?php echo e($aiSuggestion->status === 'generated' ? 'warning' : ($aiSuggestion->status === 'accepted' ? 'success' : 'danger')); ?>">
                            <?php echo e(ucfirst($aiSuggestion->status)); ?>

                        </span>
                        <span class="badge bg-info"><?php echo e($aiSuggestion->ai_model); ?></span>
                        <?php if($aiSuggestion->confidence_score): ?>
                            <span class="badge bg-secondary">
                                Độ tin cậy: <?php echo e(number_format($aiSuggestion->confidence_score * 100, 1)); ?>%
                            </span>
                        <?php endif; ?>
                        <?php if($aiSuggestion->is_favorite): ?>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star me-1"></i>Yêu thích
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted">
                        <small>
                            <i class="fas fa-calendar me-1"></i>
                            Tạo lúc: <?php echo e($aiSuggestion->created_at->format('d/m/Y H:i')); ?>

                            (<?php echo e($aiSuggestion->created_at->diffForHumans()); ?>)
                        </small>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <?php if($aiSuggestion->event): ?>
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-1">Sự kiện liên quan</h6>
                                <p class="card-text">
                                    <strong><?php echo e($aiSuggestion->event->name); ?></strong><br>
                                    <small class="text-muted"><?php echo e($aiSuggestion->event->type); ?></small>
                                </p>
                                <a href="<?php echo e(route('events.show', $aiSuggestion->event)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Xem sự kiện
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- AI Suggestion Content -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Nội dung gợi ý AI
                    </h5>
                </div>
                <div class="card-body">
                    <div class="ai-content">
                        <?php echo nl2br(e($aiSuggestion->content)); ?>

                    </div>
                </div>
            </div>

            <!-- User Feedback -->
            <?php if($aiSuggestion->user_feedback): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-comment me-2"></i>Phản hồi của người dùng
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo e($aiSuggestion->user_feedback); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Implementation Notes -->
            <?php if($aiSuggestion->implementation_notes): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Ghi chú triển khai
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo e($aiSuggestion->implementation_notes); ?></p>
                    </div>
                </div>
            <?php endif; ?>


        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Thông tin chi tiết
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 text-primary"><?php echo e($aiSuggestion->ai_model); ?></div>
                                <small class="text-muted">AI Model</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 text-info">
                                    <?php echo e($aiSuggestion->confidence_score ? number_format($aiSuggestion->confidence_score * 100, 1) . '%' : 'N/A'); ?>

                                </div>
                                <small class="text-muted">Độ tin cậy</small>
                            </div>
                        </div>
                        <?php if($aiSuggestion->rating): ?>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-warning">
                                        <?php echo e($aiSuggestion->rating); ?>/5
                                    </div>
                                    <small class="text-muted">Đánh giá</small>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($aiSuggestion->estimated_cost): ?>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-success">
                                        <?php echo e(number_format($aiSuggestion->estimated_cost)); ?>đ
                                    </div>
                                    <small class="text-muted">Chi phí ước tính</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Input Parameters -->
            <?php if($aiSuggestion->input_parameters): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>Tham số đầu vào
                        </h6>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded"><code><?php echo e(json_encode($aiSuggestion->input_parameters, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Suppliers -->
            <?php if($aiSuggestion->related_suppliers): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Nhà cung cấp liên quan
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php $__currentLoopData = $aiSuggestion->related_suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><?php echo e($supplier['name'] ?? 'N/A'); ?></span>
                                <?php if(isset($supplier['contact'])): ?>
                                    <small class="text-muted"><?php echo e($supplier['contact']); ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Tags -->
            <?php if($aiSuggestion->tags): ?>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-tags me-2"></i>Tags
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php $__currentLoopData = $aiSuggestion->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-secondary me-1 mb-1"><?php echo e($tag); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function updateStatus(suggestionId, status) {
    if (confirm('Bạn có chắc chắn muốn ' + (status === 'accepted' ? 'chấp nhận' : 'từ chối') + ' gợi ý này?')) {
        fetch(`<?php echo e(url('/ai-suggestions')); ?>/${suggestionId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái');
        });
    }
}

function toggleFavorite(suggestionId) {
    fetch(`<?php echo e(url('/ai-suggestions')); ?>/${suggestionId}/favorite`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật yêu thích');
    });
}


</script>

<style>
.ai-content {
    line-height: 1.8;
    font-size: 1.05em;
}

.ai-content h1, .ai-content h2, .ai-content h3, .ai-content h4, .ai-content h5, .ai-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.ai-content ul, .ai-content ol {
    margin-left: 1.5rem;
    margin-bottom: 1rem;
}

.ai-content li {
    margin-bottom: 0.5rem;
}

.ai-content strong {
    color: #2c3e50;
}

.ai-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
}

.ai-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
}
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views\ai-suggestions\show.blade.php ENDPATH**/ ?>