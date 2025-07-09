<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TacGia;
use App\Models\TheLoai;
use App\Models\NhaXuatBan;

class Sach extends Model
{
    use HasFactory;
    protected $table = 'SACH';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'MaSach',
        'TenSach',
        'MaTacGia',
        'MaNhaXuatBan',
        'NamXuatBan',
        'NgayNhap',
        'TriGia',
        'TinhTrang'
    ];

    protected $casts = [
        'NgayNhap' => 'date',
        'TinhTrang' => 'integer',
        'TriGia' => 'decimal:2',
    ];

    // Constants for TinhTrang
    const TINH_TRANG_DANG_MUON = 0;    // Đang được mượn
    const TINH_TRANG_CO_SAN = 1;       // Có sẵn (có thể mượn)
    const TINH_TRANG_HONG = 3;         // Hỏng (không thể mượn)
    const TINH_TRANG_BI_MAT = 4;       // Bị mất (không thể mượn)

    // Quan hệ one-to-many với TacGia (mỗi sách có 1 tác giả)
    public function tacGia()
    {
        return $this->belongsTo(TacGia::class, 'MaTacGia', 'id');
    }

    // Quan hệ many-to-many với TheLoai (sách có thể thuộc nhiều thể loại)
    public function theLoais()
    {
        return $this->belongsToMany(TheLoai::class, 'SACH_THELOAI', 'sach_id', 'theloai_id');
    }
    
    // Quan hệ one-to-many với NhaXuatBan (mỗi sách có 1 nhà xuất bản)
    public function nhaXuatBan()
    {
        return $this->belongsTo(NhaXuatBan::class, 'MaNhaXuatBan', 'id');
    }

    // Methods to manage book status (only called by system)
    public function markAsBorrowed()
    {
        $this->TinhTrang = self::TINH_TRANG_DANG_MUON;
        $this->save();
    }

    public function markAsAvailable()
    {
        $this->TinhTrang = self::TINH_TRANG_CO_SAN;
        $this->save();
    }

    public function markAsDamaged()
    {
        $this->TinhTrang = self::TINH_TRANG_HONG;
        $this->save();
    }
    
    public function markAsLost()
    {
        $this->TinhTrang = self::TINH_TRANG_BI_MAT;
        $this->save();
    }

    // Check if book is available for borrowing
    public function isAvailable()
    {
        return $this->TinhTrang == self::TINH_TRANG_CO_SAN;
    }

    // Check if book is currently borrowed
    public function isBorrowed()
    {
        return $this->TinhTrang == self::TINH_TRANG_DANG_MUON;
    }

    // Check if book is damaged
    public function isDamaged()
    {
        return $this->TinhTrang == self::TINH_TRANG_HONG;
    }

    // Check if book is lost
    public function isLost()
    {
        return $this->TinhTrang == self::TINH_TRANG_BI_MAT;
    }

    // Check if book can be borrowed (available and not damaged/lost)
    public function canBeBorrowed()
    {
        return $this->TinhTrang == self::TINH_TRANG_CO_SAN;
    }

    // Override create method to ensure default status
    public static function create(array $attributes = [])
    {
        // Remove TinhTrang from attributes if present
        unset($attributes['TinhTrang']);
        
        // Let the database default handle TinhTrang
        return static::query()->create($attributes);
    }

    public function getTinhTrangTextAttribute()
    {
        switch ($this->TinhTrang) {
            case self::TINH_TRANG_CO_SAN:
                return 'Có sẵn';
            case self::TINH_TRANG_DANG_MUON:
                return 'Đang được mượn';
            case self::TINH_TRANG_HONG:
                return 'Hỏng';
            case self::TINH_TRANG_BI_MAT:
                return 'Bị mất';
            default:
                return 'Không xác định';
        }
    }

    // Get status color for display
    public function getTinhTrangColorAttribute()
    {
        switch ($this->TinhTrang) {
            case self::TINH_TRANG_CO_SAN:
                return 'success'; // green
            case self::TINH_TRANG_DANG_MUON:
                return 'warning'; // orange
            case self::TINH_TRANG_HONG:
                return 'danger'; // red
            case self::TINH_TRANG_BI_MAT:
                return 'dark'; // black/gray
            default:
                return 'secondary';
        }
    }

    // Scope to get only available books
    public function scopeAvailable($query)
    {
        return $query->where('TinhTrang', self::TINH_TRANG_CO_SAN);
    }

    // Scope to get only borrowed books
    public function scopeBorrowed($query)
    {
        return $query->where('TinhTrang', self::TINH_TRANG_DANG_MUON);
    }

    // Scope to get books that can be borrowed (only available)
    public function scopeBorrowable($query)
    {
        return $query->where('TinhTrang', self::TINH_TRANG_CO_SAN);
    }

    // Scope to get damaged books
    public function scopeDamaged($query)
    {
        return $query->where('TinhTrang', self::TINH_TRANG_HONG);
    }

    // Scope to get lost books
    public function scopeLost($query)
    {
        return $query->where('TinhTrang', self::TINH_TRANG_BI_MAT);
    }

    // Scope to get problematic books (damaged or lost)
    public function scopeProblematic($query)
    {
        return $query->whereIn('TinhTrang', [self::TINH_TRANG_HONG, self::TINH_TRANG_BI_MAT]);
    }
}

