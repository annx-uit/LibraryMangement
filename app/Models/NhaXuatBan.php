<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sach;

class NhaXuatBan extends Model
{
    protected $table = 'NHAXUATBAN';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'TenNXB',
    ];

    public $timestamps = false;

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'MaNhaXuatBan', 'id');
    }
}
