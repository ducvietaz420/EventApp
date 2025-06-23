<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Hiển thị lịch sử hoạt động của tất cả users (chỉ admin)
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
                           ->orderBy('created_at', 'desc');

        // Lọc theo user nếu có
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo action nếu có
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Lọc theo từ khóa tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $activityLogs = $query->paginate(20)->withQueryString();

        // Lấy danh sách users để hiển thị filter
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();

        // Các action có sẵn
        $actions = [
            ActivityLog::ACTION_CREATE => 'Tạo mới',
            ActivityLog::ACTION_UPDATE => 'Cập nhật',
            ActivityLog::ACTION_DELETE => 'Xóa',
            ActivityLog::ACTION_LOGIN => 'Đăng nhập',
            ActivityLog::ACTION_LOGOUT => 'Đăng xuất',
            ActivityLog::ACTION_EXPORT => 'Xuất dữ liệu',
            ActivityLog::ACTION_UPLOAD => 'Tải lên',
            ActivityLog::ACTION_DOWNLOAD => 'Tải xuống',
            ActivityLog::ACTION_VIEW => 'Xem',
            ActivityLog::ACTION_STATUS_CHANGE => 'Thay đổi trạng thái',
        ];

        return view('activity-logs.index', compact('activityLogs', 'users', 'actions'));
    }



    /**
     * Hiển thị chi tiết lịch sử hoạt động của một user cụ thể (chỉ admin)
     */
    public function userActivities(Request $request, User $user)
    {
        $query = ActivityLog::where('user_id', $user->id)
                           ->orderBy('created_at', 'desc');

        // Lọc theo action nếu có
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Lọc theo từ khóa tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%");
        }

        $activityLogs = $query->paginate(20)->withQueryString();

        // Các action có sẵn
        $actions = [
            ActivityLog::ACTION_CREATE => 'Tạo mới',
            ActivityLog::ACTION_UPDATE => 'Cập nhật',
            ActivityLog::ACTION_DELETE => 'Xóa',
            ActivityLog::ACTION_LOGIN => 'Đăng nhập',
            ActivityLog::ACTION_LOGOUT => 'Đăng xuất',
            ActivityLog::ACTION_EXPORT => 'Xuất dữ liệu',
            ActivityLog::ACTION_UPLOAD => 'Tải lên',
            ActivityLog::ACTION_DOWNLOAD => 'Tải xuống',
            ActivityLog::ACTION_VIEW => 'Xem',
            ActivityLog::ACTION_STATUS_CHANGE => 'Thay đổi trạng thái',
        ];

        return view('activity-logs.user-activities', compact('activityLogs', 'user', 'actions'));
    }

    /**
     * Hiển thị chi tiết một log cụ thể (chỉ admin)
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');

        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Xóa lịch sử hoạt động cũ (chỉ admin)
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = Carbon::now()->subDays($request->days);
        
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('activity-logs.index')
                        ->with('success', "Đã xóa {$deletedCount} bản ghi lịch sử hoạt động cũ hơn {$request->days} ngày.");
    }
}
