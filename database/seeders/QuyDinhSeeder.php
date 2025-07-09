<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuyDinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quyDinhData = [
            [
                'TenQuyDinh' => 'Tuổi tối thiểu độc giả',
                'GiaTri' => '18',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TenQuyDinh' => 'Tuổi tối đa độc giả',
                'GiaTri' => '55',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TenQuyDinh' => 'Thời hạn thẻ độc giả (tháng)',
                'GiaTri' => '6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TenQuyDinh' => 'Số lượng sách tối đa mượn cùng lúc',
                'GiaTri' => '5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TenQuyDinh' => 'Số ngày mượn tối đa',
                'GiaTri' => '4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TenQuyDinh' => 'Số năm xuất bản sách được chấp nhận',
                'GiaTri' => '8',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('QUYDINH')->insert($quyDinhData);
    }
}
