<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Sach;

class TacGia extends Model
{
    use HasFactory;
    protected $table = 'TACGIA';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'TenTacGia',
    ];

    public $timestamps = false;

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'MaTacGia', 'id');
    }
}
