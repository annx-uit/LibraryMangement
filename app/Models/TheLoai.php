<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Sach;

class TheLoai extends Model
{
    use HasFactory;
    protected $table = 'THELOAI';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'TenTheLoai',
    ];

    public $timestamps = false;

    public function saches()
    {
        return $this->belongsToMany(Sach::class, 'SACH_THELOAI', 'theloai_id', 'sach_id');
    }
}
