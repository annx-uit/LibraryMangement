<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiDocGia extends Model
{
    use HasFactory;

    protected $table = 'LOAIDOCGIA';

    protected $fillable = [
        'TenLoaiDocGia'
    ];

    public $timestamps = false;

    // Relationship với DocGia
    public function docGias()
    {
        return $this->hasMany(DocGia::class, 'loaidocgia_id');
    }
}
