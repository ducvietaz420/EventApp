<?php $__env->startSection('title', 'Dashboard - Event Management'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('page-actions'); ?>
    <a href="<?php echo e(route('events.create')); ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <!-- Thống kê tổng quan -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng số sự kiện
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($totalEvents ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sự kiện hoàn thành
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($completedEvents ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Đang tiến hành
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($inProgressEvents ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tổng ngân sách
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo e(number_format($totalBudget ?? 0, 0, ',', '.')); ?> VNĐ
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sự kiện sắp tới -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Sự kiện sắp tới</h6>
                <a href="<?php echo e(route('events.index')); ?>" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <?php if(isset($upcomingEventsList) && $upcomingEventsList->count() > 0): ?>
                    <?php $__currentLoopData = $upcomingEventsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    <a href="<?php echo e(route('events.show', $event->id)); ?>" class="text-decoration-none">
                                        <?php echo e($event->name); ?>

                                    </a>
                                </h6>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo e($event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d/m/Y H:i') : 'Chưa xác định'); ?>

                                </p>
                                <span class="status-badge status-<?php echo e($event->status); ?>">
                                    <?php echo e($event->status_display); ?>

                                </span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Không có sự kiện sắp tới</p>
                        <a href="<?php echo e(route('events.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Tạo sự kiện mới
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Nhiệm vụ cần hoàn thành -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Nhiệm vụ cần hoàn thành</h6>
                <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <?php if(isset($pendingTasks) && $pendingTasks->count() > 0): ?>
                    <?php $__currentLoopData = $pendingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-tasks"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?php echo e($task->title); ?></h6>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-calendar me-1"></i>
                                    Sự kiện: 
                                    <a href="<?php echo e(route('events.show', $task->event_id)); ?>" class="text-decoration-none">
                                        <?php echo e($task->event->name ?? 'N/A'); ?>

                                    </a>
                                </p>
                                <span class="badge bg-<?php echo e($task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'secondary')); ?>">
                                    <?php echo e(ucfirst($task->priority)); ?>

                                </span>
                            </div>
                            <div class="flex-shrink-0">
                                <form action="#" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="btn btn-sm btn-success" title="Đánh dấu hoàn thành">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tất cả nhiệm vụ đã hoàn thành!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Biểu đồ thống kê -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thống kê sự kiện theo tháng</h6>
            </div>
            <div class="card-body">
                <canvas id="eventsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Phân bố theo loại sự kiện</h6>
            </div>
            <div class="card-body">
                <canvas id="eventTypesChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ sự kiện theo tháng - sử dụng dữ liệu thực từ database
const ctx1 = document.getElementById('eventsChart').getContext('2d');
const eventsChart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthlyLabels ?? [], 15, 512) ?>,
        datasets: [{
            label: 'Số sự kiện',
            data: <?php echo json_encode($monthlyData ?? [], 15, 512) ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: true
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Số sự kiện: ' + context.parsed.y;
                    }
                }
            }
        }
    }
});

// Biểu đồ phân bố loại sự kiện - sử dụng dữ liệu thực từ database
const ctx2 = document.getElementById('eventTypesChart').getContext('2d');
const eventTypesChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($typeLabels ?? ['Chưa có dữ liệu'], 15, 512) ?>,
        datasets: [{
            data: <?php echo json_encode($typeData ?? [0], 15, 512) ?>,
            backgroundColor: [
                '#FF6384',
                '#36A2EB', 
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\appevent\appevent\resources\views/dashboard.blade.php ENDPATH**/ ?>