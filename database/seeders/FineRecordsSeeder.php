<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChiTietPhieuMuon;
use App\Models\PhieuThuTienPhat;
use App\Models\DocGia;
use Carbon\Carbon;

class FineRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cập nhật tiền phạt cho một số chi tiết phiếu mượn đã trả
        $chiTietList = ChiTietPhieuMuon::whereNotNull('NgayTra')->take(5)->get();
        
        foreach ($chiTietList as $chiTiet) {
            $fineAmount = rand(10000, 50000); // 10k-50k VND
            $chiTiet->update(['TienPhat' => $fineAmount]);
        }

        // Tạo một số phiếu thu tiền phạt mẫu (đã thanh toán)
        $readers = DocGia::take(3)->get();
        
        foreach ($readers as $reader) {
            $maPhieu = PhieuThuTienPhat::generateMaPhieu();
            
            PhieuThuTienPhat::create([
                'MaPhieu' => $maPhieu,
                'docgia_id' => $reader->id,
                'SoTienNop' => rand(15000, 80000)
            ]);
        }

        echo "Đã tạo dữ liệu mẫu cho phiếu phạt\n";
    }
}
