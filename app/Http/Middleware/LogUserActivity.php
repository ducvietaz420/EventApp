<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chỉ log khi user đã đăng nhập
        if (auth()->check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    private function logActivity(Request $request, Response $response)
    {
        // Chỉ log các request thành công và không phải AJAX
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300 && !$request->ajax()) {
            $route = $request->route();
            
            if (!$route) {
                return;
            }

            $routeName = $route->getName();
            $method = $request->method();
            
            // Bỏ qua một số route không cần log
            $skipRoutes = [
                'activity-logs.index',
                'activity-logs.my-activities', 
                'activity-logs.show',
                'activity-logs.user-activities',
                'api.',
            ];

            foreach ($skipRoutes as $skipRoute) {
                if (str_contains($routeName, $skipRoute)) {
                    return;
                }
            }

            $description = $this->generateDescription($routeName, $method, $request);
            
            if ($description) {
                $action = $this->mapRouteToAction($routeName, $method);
                
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'model_type' => $this->getModelType($routeName),
                    'model_id' => $this->getModelId($request, $routeName),
                ]);
            }
        }
    }

    private function generateDescription(string $routeName, string $method, Request $request): ?string
    {
        $routeMap = [
            // Authentication
            'login' => 'Đăng nhập vào hệ thống',
            'logout' => 'Đăng xuất khỏi hệ thống',
            
            // Dashboard
            'dashboard' => 'Truy cập trang dashboard',
            
            // Events
            'events.index' => 'Xem danh sách sự kiện',
            'events.create' => 'Truy cập trang tạo sự kiện',
            'events.store' => 'Tạo sự kiện mới',
            'events.show' => 'Xem chi tiết sự kiện',
            'events.edit' => 'Truy cập trang chỉnh sửa sự kiện',
            'events.update' => 'Cập nhật thông tin sự kiện',
            'events.destroy' => 'Xóa sự kiện',
            'events.export' => 'Xuất danh sách sự kiện',
            'events.export.detail' => 'Xuất chi tiết sự kiện',
            
            // Event Images
            'events.images.index' => 'Xem hình ảnh sự kiện',
            'events.images.upload' => 'Upload hình ảnh sự kiện',
            'events.images.delete' => 'Xóa hình ảnh sự kiện',
            'events.images.download-zip' => 'Tải xuống hình ảnh sự kiện',
            
            // Users
            'users.index' => 'Xem danh sách người dùng',
            'users.create' => 'Truy cập trang tạo người dùng',
            'users.store' => 'Tạo người dùng mới',
            'users.show' => 'Xem thông tin người dùng',
            'users.edit' => 'Truy cập trang chỉnh sửa người dùng',
            'users.update' => 'Cập nhật thông tin người dùng',
            'users.destroy' => 'Xóa người dùng',
            'users.toggle-status' => 'Thay đổi trạng thái người dùng',
            'users.permissions' => 'Quản lý phân quyền người dùng',
            
            // Checklists
            'checklists.index' => 'Xem danh sách công việc',
            'checklists.create' => 'Truy cập trang tạo công việc',
            'checklists.store' => 'Tạo công việc mới',
            'checklists.show' => 'Xem chi tiết công việc',
            'checklists.edit' => 'Truy cập trang chỉnh sửa công việc',
            'checklists.update' => 'Cập nhật thông tin công việc',
            'checklists.destroy' => 'Xóa công việc',
            'checklists.updateStatus' => 'Cập nhật trạng thái công việc',
            'checklists.export' => 'Xuất danh sách công việc',
            
            // AI Suggestions
            'ai-suggestions.index' => 'Xem danh sách gợi ý AI',
            'ai-suggestions.create' => 'Truy cập trang tạo gợi ý AI',
            'ai-suggestions.store' => 'Tạo gợi ý AI mới',
            'ai-suggestions.show' => 'Xem chi tiết gợi ý AI',
            'ai-suggestions.edit' => 'Truy cập trang chỉnh sửa gợi ý AI',
            'ai-suggestions.update' => 'Cập nhật gợi ý AI',
            'ai-suggestions.destroy' => 'Xóa gợi ý AI',
            'ai-suggestions.generate' => 'Tạo gợi ý AI tự động',
        ];

        return $routeMap[$routeName] ?? null;
    }

    private function mapRouteToAction(string $routeName, string $method): string
    {
        if (str_contains($routeName, '.store')) {
            return ActivityLog::ACTION_CREATE;
        }
        
        if (str_contains($routeName, '.update') || str_contains($routeName, '.toggle-status')) {
            return ActivityLog::ACTION_UPDATE;
        }
        
        if (str_contains($routeName, '.destroy')) {
            return ActivityLog::ACTION_DELETE;
        }
        
        if (str_contains($routeName, 'login')) {
            return ActivityLog::ACTION_LOGIN;
        }
        
        if (str_contains($routeName, 'logout')) {
            return ActivityLog::ACTION_LOGOUT;
        }
        
        if (str_contains($routeName, 'export') || str_contains($routeName, 'download')) {
            return ActivityLog::ACTION_EXPORT;
        }
        
        if (str_contains($routeName, 'upload')) {
            return ActivityLog::ACTION_UPLOAD;
        }
        
        return ActivityLog::ACTION_VIEW;
    }

    private function getModelType(string $routeName): ?string
    {
        if (str_contains($routeName, 'events')) {
            return 'App\Models\Event';
        }
        
        if (str_contains($routeName, 'users')) {
            return 'App\Models\User';
        }
        
        if (str_contains($routeName, 'checklists')) {
            return 'App\Models\Checklist';
        }
        
        if (str_contains($routeName, 'ai-suggestions')) {
            return 'App\Models\AiSuggestion';
        }
        
        return null;
    }

    private function getModelId(Request $request, string $routeName): ?int
    {
        $route = $request->route();
        
        if (!$route) {
            return null;
        }

        // Lấy ID từ route parameters
        $parameters = $route->parameters();
        
        foreach ($parameters as $key => $value) {
            if (is_object($value) && method_exists($value, 'getKey')) {
                return $value->getKey();
            }
            
            if (is_numeric($value)) {
                return (int) $value;
            }
        }
        
        return null;
    }
}
