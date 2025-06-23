<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <title><?php echo $__env->yieldContent('title', 'Event Management System'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }
        .status-planning { background-color: #e3f2fd; color: #1976d2; }
        .status-in-progress { background-color: #fff3e0; color: #f57c00; }
        .status-completed { background-color: #e8f5e8; color: #388e3c; }
        .status-cancelled { background-color: #ffebee; color: #d32f2f; }
        .avatar-circle {
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="navbar-brand">Quản lý sự kiện</h4>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(auth()->user()->hasPermission('events.view')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('events.*') ? 'active' : ''); ?>" href="<?php echo e(route('events.index')); ?>">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Sự kiện
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(auth()->user()->hasPermission('checklists.view')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('checklists.*') ? 'active' : ''); ?>" href="<?php echo e(route('checklists.index')); ?>">
                                        <i class="fas fa-check-square me-2"></i>
                                        Danh sách công việc
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(auth()->user()->hasPermission('ai_suggestions.view')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('ai-suggestions.*') ? 'active' : ''); ?>" href="<?php echo e(route('ai-suggestions.index')); ?>">
                                        <i class="fas fa-robot me-2"></i>
                                        Gợi ý AI
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(auth()->user()->hasPermission('users.view')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>">
                                        <i class="fas fa-users me-2"></i>
                                        Quản lý người dùng
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if(auth()->user()->hasPermission('activity_logs.view_all')): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('activity-logs.*') ? 'active' : ''); ?>" href="<?php echo e(route('activity-logs.index')); ?>">
                                        <i class="fas fa-history me-2"></i>
                                        Lịch sử hoạt động
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- User Menu -->
                    <?php if(auth()->guard()->check()): ?>
                        <hr class="text-white">
                        <div class="dropdown">
                            <button class="btn btn-link nav-link dropdown-toggle text-white text-decoration-none w-100 text-start" 
                                    type="button" 
                                    id="userDropdown" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?php echo e(auth()->user()->name); ?></div>
                                        <small class="opacity-75"><?php echo e(auth()->user()->role_display); ?></small>
                                    </div>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user me-2"></i>Thông tin cá nhân
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <?php echo $__env->yieldContent('page-actions'); ?>
                    </div>
                </div>
                
                <!-- Alerts -->
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Có lỗi xảy ra:</strong>
                        <ul class="mb-0 mt-2">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\appevent\appevent\resources\views/layouts/app.blade.php ENDPATH**/ ?>