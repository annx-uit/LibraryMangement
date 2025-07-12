<?php

namespace Database\Factories;

use App\Models\ChiTietPhieuMuon;
use App\Models\PhieuMuon;
use App\Models\Sach;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ChiTietPhieuMuonFactory extends Factory
{
    protected $model = ChiTietPhieuMuon::class;

    public function definition()
    {
        return [
            'phieumuon_id' => PhieuMuon::factory(),
            'sach_id' => Sach::factory(),
            'NgayTra' => null,
            'TienPhat' => 0,
            'TienDenBu' => 0
        ];
    }

    public function returned()
    {
        return $this->state(function (array $attributes) {
            return [
                'NgayTra' => Carbon::now(),
                'TienPhat' => $this->faker->numberBetween(0, 50000),
                'TienDenBu' => $this->faker->numberBetween(0, 100000),
            ];
        });
    }


} 