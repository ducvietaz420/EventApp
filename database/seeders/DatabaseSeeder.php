<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        // Tạo permissions và roles trước
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        
        // Tạo tài khoản admin trước
        $this->call(AdminUserSeeder::class);
        
        // Chạy EventSeeder để tạo dữ liệu mẫu cho hệ thống quản lý sự kiện
        $this->call(EventSeeder::class);
    }
}
