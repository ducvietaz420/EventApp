<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin có toàn quyền
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Kiểm tra quyền cụ thể - chỉ cần một trong các quyền
        if (!empty($permissions)) {
            $hasPermission = false;
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasPermission = true;
                    break;
                }
            }
            
            if (!$hasPermission) {
                abort(403, 'Bạn không có quyền truy cập chức năng này.');
            }
        }

        return $next($request);
    }
}
