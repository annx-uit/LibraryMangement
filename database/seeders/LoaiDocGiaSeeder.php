<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoaiDocGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('LOAIDOCGIA')->delete();
        
        $loaiDocGias = [
            'Sinh viên',
            'Giảng viên', 
            'Cán bộ nhân viên',
            'Độc giả bên ngoài',
            'Học sinh',
            'Nghiên cứu sinh'
        ];

        foreach ($loaiDocGias as $loai) {
            DB::table('LOAIDOCGIA')->insert([
                'TenLoaiDocGia' => $loai
            ]);
        }
    }
}
