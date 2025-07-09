<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('QUYDINH', function (Blueprint $table) {
            $table->id();
            $table->string('TenQuyDinh')->unique();
            $table->string('GiaTri');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('QUYDINH');
    }
};