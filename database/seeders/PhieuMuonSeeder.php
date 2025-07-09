<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PhieuMuon;
use App\Models\ChiTietPhieuMuon;
use App\Models\DocGia;
use App\Models\Sach;
use Carbon\Carbon;

class PhieuMuonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample borrow records
        $borrowRecords = [
            [
                'docgia_id' => 1,
                'sach_id' => 1,
                'NgayMuon' => Carbon::now()->subDays(5),
                'NgayTra' => null,
            ],
            [
                'docgia_id' => 2,
                'sach_id' => 2,
                'NgayMuon' => Carbon::now()->subDays(20), // Overdue
                'NgayTra' => null,
            ],
            [
                'docgia_id' => 3,
                'sach_id' => 3,
                'NgayMuon' => Carbon::now()->subDays(12), // Due soon
                'NgayTra' => null,
            ],
            [
                'docgia_id' => 1,
                'sach_id' => 4,
                'NgayMuon' => Carbon::now()->subDays(30),
                'NgayTra' => Carbon::now()->subDays(2), // Returned with fine
            ],
        ];

        foreach ($borrowRecords as $record) {
            // Check if reader and book exist
            $reader = DocGia::find($record['docgia_id']);
            $book = Sach::find($record['sach_id']);
            
            if ($reader && $book) {
                $phieuMuon = new PhieuMuon([
                    'docgia_id' => $record['docgia_id'],
                    'NgayMuon' => $record['NgayMuon'],
                ]);
                $phieuMuon->MaPhieu = $phieuMuon->generateMaPhieu();
                $phieuMuon->save();

                // Calculate fine if returned late
                $fine = 0;
                if ($record['NgayTra']) {
                    $dueDate = Carbon::parse($record['NgayMuon'])->addDays(14);
                    $returnDate = Carbon::parse($record['NgayTra']);
                    
                    if ($returnDate->isAfter($dueDate)) {
                        $overdueDays = $returnDate->diffInDays($dueDate);
                        $fine = $overdueDays * 1000; // 1,000 VND per day
                    }
                }

                ChiTietPhieuMuon::create([
                    'phieumuon_id' => $phieuMuon->id,
                    'sach_id' => $record['sach_id'],
                    'NgayTra' => $record['NgayTra'],
                    'TienPhat' => $fine,
                ]);
            }
        }
    }
}
