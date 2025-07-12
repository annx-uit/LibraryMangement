<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VaiTro;

class VaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed vai trò mặc định
        $roles = [
            ['VaiTro' => 'Admin'],
            ['VaiTro' => 'Thủ thư'],
        ];

        foreach ($roles as $role) {
            VaiTro::firstOrCreate(['VaiTro' => $role['VaiTro']], $role);
        }
    }
}
