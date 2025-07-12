<?php

namespace Database\Factories;

use App\Models\LoaiDocGia;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoaiDocGiaFactory extends Factory
{
    protected $model = LoaiDocGia::class;

    public function definition()
    {
        return [
            'TenLoaiDocGia' => $this->faker->unique()->word . ' ' . $this->faker->unique()->randomNumber(3),
        ];
    }
} 