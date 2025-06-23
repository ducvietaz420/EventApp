<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tìm role admin
        $adminRole = Role::where('name', 'admin')->first();
        
        // Tạo tài khoản admin mặc định
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'role_id' => $adminRole?->id,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Tạo user demo với role user
        $userRole = Role::where('name', 'user')->first();
        User::firstOrCreate(
            ['email' => 'user@demo.com'],
            [
                'name' => 'Demo User',
                'username' => 'demouser',
                'email' => 'user@demo.com',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_USER,
                'role_id' => $userRole?->id,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Tạo manager demo
        $managerRole = Role::where('name', 'manager')->first();
        User::firstOrCreate(
            ['email' => 'manager@demo.com'],
            [
                'name' => 'Demo Manager',
                'username' => 'demomanager',
                'email' => 'manager@demo.com',
                'password' => Hash::make('123456'),
                'role' => User::ROLE_USER, // Giữ role cũ cho tương thích
                'role_id' => $managerRole?->id,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Đã tạo các tài khoản demo:');
        $this->command->line('Admin - Username: admin, Password: admin123');
        $this->command->line('Manager - Username: demomanager, Password: 123456');
        $this->command->line('User - Username: demouser, Password: 123456');
    }
}
