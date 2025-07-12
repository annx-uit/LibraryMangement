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
        // Thêm dữ liệu quy định mặc định
        $thamSoData = [
            [
                'TenThamSo' => 'TuoiToiThieu',
                'GiaTri' => '18',
            ],
            [
                'TenThamSo' => 'TuoiToiDa',
                'GiaTri' => '55',
            ],
            [
                'TenThamSo' => 'ThoiHanThe',
                'GiaTri' => '6',
            ],
            [
                'TenThamSo' => 'SoSachToiDa',
                'GiaTri' => '5',
            ],
            [
                'TenThamSo' => 'NgayMuonToiDa',
                'GiaTri' => '14',
            ],
            [
                'TenThamSo' => 'SoNamXuatBan',
                'GiaTri' => '8',
            ],
        ];

        // Chỉ thêm nếu chưa tồn tại
        foreach ($thamSoData as $thamSo) {
            if (!DB::table('THAMSO')->where('TenThamSo', $thamSo['TenThamSo'])->exists()) {
                DB::table('THAMSO')->insert($thamSo);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa tất cả dữ liệu quy định khi rollback
        DB::table('THAMSO')->truncate();
    }
};
