<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sach;

class TacGia extends Model
{
    protected $table = 'TACGIA';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = [
        'TenTacGia',
    ];

    public function sachs()
    {
        return $this->hasMany(Sach::class, 'MaTacGia', 'id');
    }
}
