<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Đảm bảo bảng VAITRO đã tồn tại và có vai trò Admin
        if (!DB::table('VAITRO')->where('VaiTro', 'Admin')->exists()) {
            DB::table('VAITRO')->insert([
                'VaiTro' => 'Admin'
            ]);
        }

        // Lấy ID của vai trò Admin
        $adminRoleId = DB::table('VAITRO')->where('VaiTro', 'Admin')->value('id');

        // Tạo tài khoản admin nếu chưa tồn tại
        if (!DB::table('TAIKHOAN')->where('Email', 'admin@library.com')->exists()) {
            DB::table('TAIKHOAN')->insert([
                'HoVaTen' => 'Administrator',
                'Email' => 'admin@library.com',
                'MatKhau' => Hash::make('123456'),
                'vaitro_id' => $adminRoleId,
            ]);
        }

        // Tạo thêm tài khoản thủ thư nếu chưa có
        if (!DB::table('VAITRO')->where('VaiTro', 'Thủ thư')->exists()) {
            DB::table('VAITRO')->insert([
                'VaiTro' => 'Thủ thư'
            ]);
        }

        $librarianRoleId = DB::table('VAITRO')->where('VaiTro', 'Thủ thư')->value('id');

        if (!DB::table('TAIKHOAN')->where('Email', 'librarian@library.com')->exists()) {
            DB::table('TAIKHOAN')->insert([
                'HoVaTen' => 'Thủ thư',
                'Email' => 'librarian@library.com',
                'MatKhau' => Hash::make('123456'),
                'vaitro_id' => $librarianRoleId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa tài khoản admin khi rollback
        DB::table('TAIKHOAN')->where('Email', 'admin@library.com')->delete();
        DB::table('TAIKHOAN')->where('Email', 'librarian@library.com')->delete();
        
        // Xóa vai trò nếu không còn tài khoản nào sử dụng
        if (!DB::table('TAIKHOAN')->where('vaitro_id', DB::table('VAITRO')->where('VaiTro', 'Admin')->value('id'))->exists()) {
            DB::table('VAITRO')->where('VaiTro', 'Admin')->delete();
        }
        
        if (!DB::table('TAIKHOAN')->where('vaitro_id', DB::table('VAITRO')->where('VaiTro', 'Thủ thư')->value('id'))->exists()) {
            DB::table('VAITRO')->where('VaiTro', 'Thủ thư')->delete();
        }
    }
};
