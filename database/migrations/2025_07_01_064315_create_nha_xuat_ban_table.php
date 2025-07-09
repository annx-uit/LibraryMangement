<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('NHAXUATBAN', function (Blueprint $table) {
            $table->id();
            $table->string('TenNXB');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('NHAXUATBAN');
    }
};