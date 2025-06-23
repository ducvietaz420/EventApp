<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Quản lý sự kiện
            [
                'name' => 'events.view',
                'display_name' => 'Xem sự kiện',
                'description' => 'Có thể xem danh sách và chi tiết sự kiện',
                'group' => 'events'
            ],
            [
                'name' => 'events.create',
                'display_name' => 'Tạo sự kiện',
                'description' => 'Có thể tạo sự kiện mới',
                'group' => 'events'
            ],
            [
                'name' => 'events.edit',
                'display_name' => 'Chỉnh sửa sự kiện',
                'description' => 'Có thể chỉnh sửa thông tin sự kiện',
                'group' => 'events'
            ],
            [
                'name' => 'events.delete',
                'display_name' => 'Xóa sự kiện',
                'description' => 'Có thể xóa sự kiện',
                'group' => 'events'
            ],
            [
                'name' => 'events.export',
                'display_name' => 'Xuất dữ liệu sự kiện',
                'description' => 'Có thể xuất dữ liệu sự kiện ra Excel',
                'group' => 'events'
            ],

            // Quản lý hình ảnh sự kiện
            [
                'name' => 'events.images.view',
                'display_name' => 'Xem hình ảnh sự kiện',
                'description' => 'Có thể xem hình ảnh của sự kiện',
                'group' => 'events'
            ],
            [
                'name' => 'events.images.upload',
                'display_name' => 'Upload hình ảnh',
                'description' => 'Có thể upload hình ảnh cho sự kiện',
                'group' => 'events'
            ],
            [
                'name' => 'events.images.delete',
                'display_name' => 'Xóa hình ảnh',
                'description' => 'Có thể xóa hình ảnh của sự kiện',
                'group' => 'events'
            ],
            [
                'name' => 'events.images.download',
                'display_name' => 'Tải xuống hình ảnh',
                'description' => 'Có thể tải xuống hình ảnh dưới dạng ZIP',
                'group' => 'events'
            ],

            // Quản lý checklist
            [
                'name' => 'checklists.view',
                'display_name' => 'Xem danh sách công việc',
                'description' => 'Có thể xem danh sách công việc',
                'group' => 'checklists'
            ],
            [
                'name' => 'checklists.create',
                'display_name' => 'Tạo công việc',
                'description' => 'Có thể tạo công việc mới',
                'group' => 'checklists'
            ],
            [
                'name' => 'checklists.edit',
                'display_name' => 'Chỉnh sửa công việc',
                'description' => 'Có thể chỉnh sửa công việc',
                'group' => 'checklists'
            ],
            [
                'name' => 'checklists.delete',
                'display_name' => 'Xóa công việc',
                'description' => 'Có thể xóa công việc',
                'group' => 'checklists'
            ],
            [
                'name' => 'checklists.complete',
                'display_name' => 'Hoàn thành công việc',
                'description' => 'Có thể đánh dấu công việc hoàn thành',
                'group' => 'checklists'
            ],

            // Gợi ý AI
            [
                'name' => 'ai_suggestions.view',
                'display_name' => 'Xem gợi ý AI',
                'description' => 'Có thể xem danh sách gợi ý AI',
                'group' => 'ai_suggestions'
            ],
            [
                'name' => 'ai_suggestions.generate',
                'display_name' => 'Tạo gợi ý AI',
                'description' => 'Có thể tạo gợi ý AI mới',
                'group' => 'ai_suggestions'
            ],
            [
                'name' => 'ai_suggestions.accept',
                'display_name' => 'Chấp nhận gợi ý AI',
                'description' => 'Có thể chấp nhận gợi ý AI',
                'group' => 'ai_suggestions'
            ],
            [
                'name' => 'ai_suggestions.reject',
                'display_name' => 'Từ chối gợi ý AI',
                'description' => 'Có thể từ chối gợi ý AI',
                'group' => 'ai_suggestions'
            ],

            // Quản lý người dùng
            [
                'name' => 'users.view',
                'display_name' => 'Xem danh sách người dùng',
                'description' => 'Có thể xem danh sách người dùng',
                'group' => 'users'
            ],
            [
                'name' => 'users.create',
                'display_name' => 'Tạo người dùng',
                'description' => 'Có thể tạo người dùng mới',
                'group' => 'users'
            ],
            [
                'name' => 'users.edit',
                'display_name' => 'Chỉnh sửa người dùng',
                'description' => 'Có thể chỉnh sửa thông tin người dùng',
                'group' => 'users'
            ],
            [
                'name' => 'users.delete',
                'display_name' => 'Xóa người dùng',
                'description' => 'Có thể xóa người dùng',
                'group' => 'users'
            ],
            [
                'name' => 'users.permissions',
                'display_name' => 'Quản lý phân quyền',
                'description' => 'Có thể cấp/thu hồi quyền cho người dùng',
                'group' => 'users'
            ],

            // Lịch sử hoạt động
            [
                'name' => 'activity_logs.view_own',
                'display_name' => 'Xem hoạt động của mình',
                'description' => 'Có thể xem lịch sử hoạt động của chính mình',
                'group' => 'activity_logs'
            ],
            [
                'name' => 'activity_logs.view_all',
                'display_name' => 'Xem tất cả hoạt động',
                'description' => 'Có thể xem lịch sử hoạt động của tất cả người dùng',
                'group' => 'activity_logs'
            ],
            [
                'name' => 'activity_logs.cleanup',
                'display_name' => 'Dọn dẹp lịch sử',
                'description' => 'Có thể xóa lịch sử hoạt động cũ',
                'group' => 'activity_logs'
            ],

            // Hệ thống
            [
                'name' => 'system.dashboard',
                'display_name' => 'Truy cập Dashboard',
                'description' => 'Có thể truy cập trang dashboard',
                'group' => 'system'
            ],
            [
                'name' => 'system.settings',
                'display_name' => 'Cài đặt hệ thống',
                'description' => 'Có thể thay đổi cài đặt hệ thống',
                'group' => 'system'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Đã tạo ' . count($permissions) . ' permissions');
    }
}
