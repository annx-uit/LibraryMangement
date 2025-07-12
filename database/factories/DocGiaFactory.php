<?php

namespace Database\Factories;

use App\Models\DocGia;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocGiaFactory extends Factory
{
    protected $model = DocGia::class;

    public function definition()
    {
        return [
            'HoTen' => $this->faker->name,
            'loaidocgia_id' => 1, // Sẽ override trong test nếu cần
            'NgaySinh' => $this->faker->date('Y-m-d', '-18 years'),
            'DiaChi' => $this->faker->address,
            'Email' => $this->faker->unique()->safeEmail,
            'NgayLapThe' => $this->faker->date('Y-m-d', '-1 year'),
            'NgayHetHan' => $this->faker->date('Y-m-d', '+1 year'),
            'TongNo' => 0,
        ];
    }
} 