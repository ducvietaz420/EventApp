<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra nếu cột username chưa tồn tại
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->after('name');
            });
        }
        
        // Cập nhật username cho các user hiện tại nếu chưa có
        $users = \App\Models\User::whereNull('username')->orWhere('username', '')->get();
        foreach ($users as $user) {
            $username = strtolower(str_replace(' ', '', $user->name));
            // Đảm bảo username unique
            $originalUsername = $username;
            $counter = 1;
            while (\App\Models\User::where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $originalUsername . $counter;
                $counter++;
            }
            $user->update(['username' => $username]);
        }
        
        // Thêm constraint unique và not null nếu chưa có
        if (!$this->hasUniqueConstraint('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable(false)->change();
                $table->unique('username');
            });
        }
    }
    
    private function hasUniqueConstraint($table, $column)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Column_name = '{$column}' AND Non_unique = 0");
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
