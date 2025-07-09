<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocGia;
use App\Models\LoaiDocGia;
use App\Models\Sach;
use App\Models\TacGia;
use App\Models\NhaXuatBan;
use App\Models\TheLoai;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo LoaiDocGia mẫu
        $loaiDocGia = LoaiDocGia::firstOrCreate(
            ['TenLoaiDocGia' => 'Sinh viên'],
            ['TenLoaiDocGia' => 'Sinh viên']
        );

        // Tạo DocGia mẫu
        DocGia::firstOrCreate(
            ['Email' => 'docgia1@test.com'],
            [
                'HoTen' => 'Nguyễn Văn A',
                'Email' => 'docgia1@test.com',
                'DiaChi' => '123 Đường ABC, Quận 1, TP.HCM',
                'NgaySinh' => '1995-05-15',
                'NgayLapThe' => Carbon::now()->subMonths(6),
                'NgayHetHan' => Carbon::now()->addYears(2),
                'TongNo' => 25000,
                'loaidocgia_id' => $loaiDocGia->id
            ]
        );

        DocGia::firstOrCreate(
            ['Email' => 'docgia2@test.com'],
            [
                'HoTen' => 'Trần Thị B',
                'Email' => 'docgia2@test.com',
                'DiaChi' => '456 Đường XYZ, Quận 2, TP.HCM',
                'NgaySinh' => '1990-08-20',
                'NgayLapThe' => Carbon::now()->subMonths(3),
                'NgayHetHan' => Carbon::now()->addYears(2),
                'TongNo' => 0,
                'loaidocgia_id' => $loaiDocGia->id
            ]
        );

        // Tạo TacGia mẫu
        $tacGia1 = TacGia::firstOrCreate(
            ['TenTacGia' => 'Dale Carnegie'],
            [
                'TenTacGia' => 'Dale Carnegie',
                'GhiChu' => 'Tác giả nổi tiếng về kỹ năng sống'
            ]
        );

        $tacGia2 = TacGia::firstOrCreate(
            ['TenTacGia' => 'Paulo Coelho'],
            [
                'TenTacGia' => 'Paulo Coelho',
                'GhiChu' => 'Nhà văn Brazil nổi tiếng'
            ]
        );

        // Tạo NhaXuatBan mẫu
        $nxb1 = NhaXuatBan::firstOrCreate(
            ['TenNXB' => 'NXB Tổng Hợp'],
            [
                'TenNXB' => 'NXB Tổng Hợp',
                'DiaChi' => '62 Nguyễn Thị Minh Khai, Q.3, TP.HCM'
            ]
        );

        $nxb2 = NhaXuatBan::firstOrCreate(
            ['TenNXB' => 'NXB Trẻ'],
            [
                'TenNXB' => 'NXB Trẻ',
                'DiaChi' => '161B Lý Chính Thắng, Q.3, TP.HCM'
            ]
        );

        // Tạo TheLoai mẫu
        $theLoai1 = TheLoai::firstOrCreate(
            ['TenTheLoai' => 'Kỹ năng sống'],
            ['TenTheLoai' => 'Kỹ năng sống']
        );

        $theLoai2 = TheLoai::firstOrCreate(
            ['TenTheLoai' => 'Văn học'],
            ['TenTheLoai' => 'Văn học']
        );

        // Tạo Sach mẫu
        $sach1 = Sach::firstOrCreate(
            ['TenSach' => 'Đắc Nhân Tâm'],
            [
                'MaSach' => 'S001',
                'TenSach' => 'Đắc Nhân Tâm',
                'NamXuatBan' => 2020,
                'NgayNhap' => Carbon::now()->subMonths(6),
                'TriGia' => 150000,
                'TinhTrang' => 1, // Available
                'MaTacGia' => $tacGia1->id,
                'MaNhaXuatBan' => $nxb1->id
            ]
        );

        $sach2 = Sach::firstOrCreate(
            ['TenSach' => 'Nhà Giả Kim'],
            [
                'MaSach' => 'S002',
                'TenSach' => 'Nhà Giả Kim',
                'NamXuatBan' => 2019,
                'NgayNhap' => Carbon::now()->subMonths(4),
                'TriGia' => 120000,
                'TinhTrang' => 1, // Available
                'MaTacGia' => $tacGia2->id,
                'MaNhaXuatBan' => $nxb2->id
            ]
        );

        $sach3 = Sach::firstOrCreate(
            ['TenSach' => 'Cách Giao Tiếp Hiệu Quả'],
            [
                'MaSach' => 'S003',
                'TenSach' => 'Cách Giao Tiếp Hiệu Quả',
                'NamXuatBan' => 2021,
                'NgayNhap' => Carbon::now()->subMonths(2),
                'TriGia' => 100000,
                'TinhTrang' => 1, // Available
                'MaTacGia' => $tacGia1->id,
                'MaNhaXuatBan' => $nxb1->id
            ]
        );

        $this->command->info('Đã tạo dữ liệu mẫu thành công!');
        $this->command->info('- Độc giả: ' . DocGia::count());
        $this->command->info('- Sách: ' . Sach::count());
        $this->command->info('- Tác giả: ' . TacGia::count());
        $this->command->info('- Nhà xuất bản: ' . NhaXuatBan::count());
        $this->command->info('- Thể loại: ' . TheLoai::count());
    }
}
