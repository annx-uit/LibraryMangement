<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhieuMuon extends Model
{
    use HasFactory;

    protected $table = 'PHIEUMUON';

    protected $fillable = [
        'MaPhieu',
        'docgia_id',
        'NgayMuon',
        'NgayHenTra',
    ];

    protected $casts = [
        'NgayMuon' => 'date',
        'NgayHenTra' => 'date',
    ];

    protected $appends = ['TrangThai'];

    // Boot method to handle model events
    protected static function boot()
    {
        parent::boot();
        
        // When deleting a PhieuMuon, also delete its ChiTietPhieuMuon records
        static::deleting(function ($phieuMuon) {
            $phieuMuon->chiTietPhieuMuon()->delete();
        });
    }

    public function docGia()
    {
        return $this->belongsTo(DocGia::class, 'docgia_id');
    }

    public function chiTietPhieuMuon()
    {
        return $this->hasMany(ChiTietPhieuMuon::class, 'phieumuon_id');
    }

    // Method to generate unique MaPhieu
    public static function generateMaPhieu()
    {
        $prefix = 'PM';
        $year = date('Y');
        
        // Lấy số thứ tự cao nhất trong năm hiện tại
        $latestPhieu = self::where('MaPhieu', 'LIKE', $prefix . $year . '%')
                          ->orderBy('MaPhieu', 'desc')
                          ->first();
        
        if ($latestPhieu) {
            // Lấy số thứ tự từ mã phiếu cuối cùng
            $lastNumber = (int)substr($latestPhieu->MaPhieu, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format: PM2025-0001, PM2025-0002, ...
        return $prefix . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Accessor for Trạng thái
    public function getTrangThaiAttribute()
    {
        $chiTiet = $this->chiTietPhieuMuon;
        
        if ($chiTiet->isEmpty()) {
            return 'active';
        }

        $allReturned = $chiTiet->every(function ($item) {
            return !is_null($item->NgayTra);
        });

        if ($allReturned) {
            return 'returned';
        }

        // Check for overdue books
        $hasOverdue = $chiTiet->filter(function ($item) {
            return is_null($item->NgayTra) && $this->isOverdue($item);
        })->isNotEmpty();

        if ($hasOverdue) {
            return 'overdue';
        }

        // Check for due soon books
        $hasDueSoon = $chiTiet->filter(function ($item) {
            return is_null($item->NgayTra) && $this->isDueSoon($item);
        })->isNotEmpty();

        if ($hasDueSoon) {
            return 'due-soon';
        }

        return 'active';
    }

    private function isOverdue($chiTiet)
    {
        $dueDate = Carbon::parse($this->NgayMuon)->addDays(14); // Assuming 14 days loan period
        return Carbon::now()->isAfter($dueDate);
    }

    private function isDueSoon($chiTiet)
    {
        $dueDate = Carbon::parse($this->NgayMuon)->addDays(14);
        $daysDiff = Carbon::now()->diffInDays($dueDate, false);
        return $daysDiff >= 0 && $daysDiff <= 3; // Due within 3 days
    }

    public function getFormattedNgayMuon()
    {
        return $this->NgayMuon ? $this->NgayMuon->format('d/m/Y') : '';
    }

    public function getDueDate()
    {
        if (!$this->NgayMuon) {
            return null;
        }
        
        $borrowDurationDays = QuyDinh::getBorrowDurationDays();
        return $this->NgayMuon->copy()->addDays($borrowDurationDays);
    }

    public function getFormattedDueDate()
    {
        $dueDate = $this->getDueDate();
        return $dueDate ? $dueDate->format('d/m/Y') : '';
    }
}
