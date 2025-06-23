<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Quản trị viên',
                'description' => 'Có toàn quyền truy cập hệ thống',
                'color' => '#dc3545',
                'level' => 100,
                'is_active' => true,
            ]
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Quản lý',
                'description' => 'Quản lý sự kiện và nhân viên',
                'color' => '#fd7e14',
                'level' => 50,
                'is_active' => true,
            ]
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Người dùng',
                'description' => 'Người dùng cơ bản của hệ thống',
                'color' => '#007bff',
                'level' => 10,
                'is_active' => true,
            ]
        );

        // Cấp quyền cho Admin (toàn quyền)
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Cấp quyền cho Manager
        $managerPermissions = Permission::whereIn('name', [
            'events.view',
            'events.create',
            'events.edit',
            'events.export',
            'events.images.view',
            'events.images.upload',
            'events.images.download',
            'checklists.view',
            'checklists.create',
            'checklists.edit',
            'checklists.complete',
            'ai_suggestions.view',
            'ai_suggestions.generate',
            'ai_suggestions.accept',
            'ai_suggestions.reject',
            'system.dashboard',
        ])->get();
        $managerRole->permissions()->sync($managerPermissions->pluck('id'));

        // Cấp quyền cho User (quyền cơ bản)
        $userPermissions = Permission::whereIn('name', [
            'events.view',
            'events.images.view',
            'checklists.view',
            'checklists.complete',
            'ai_suggestions.view',
            'system.dashboard',
        ])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id'));

        $this->command->info('Đã tạo 3 roles và phân quyền thành công');
        $this->command->line('- Admin: ' . $adminRole->permissions()->count() . ' quyền');
        $this->command->line('- Manager: ' . $managerRole->permissions()->count() . ' quyền');
        $this->command->line('- User: ' . $userRole->permissions()->count() . ' quyền');
    }
}
