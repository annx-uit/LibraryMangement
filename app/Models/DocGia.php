<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocGia extends Model
{
    use HasFactory;

    protected $table = 'DOCGIA';

    protected $fillable = [
        'HoTen',
        'loaidocgia_id',
        'NgaySinh',
        'DiaChi',
        'Email',
        'NgayLapThe',
        'NgayHetHan',
        'TongNo'
    ];

    // Accessor for compatibility
    public function getTenAttribute()
    {
        return $this->HoTen;
    }

    protected $casts = [
        'NgaySinh' => 'date',
        'NgayLapThe' => 'date',
        'NgayHetHan' => 'date'
    ];

    public $timestamps = false;

    // Custom date format for JSON serialization
    protected $dateFormat = 'Y-m-d';

    // Specify date format for JSON output
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    // Relationship với LoaiDocGia
    public function loaiDocGia()
    {
        return $this->belongsTo(LoaiDocGia::class, 'loaidocgia_id');
    }
}
