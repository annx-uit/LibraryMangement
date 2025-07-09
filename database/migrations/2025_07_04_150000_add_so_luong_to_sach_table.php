<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('SACH', function (Blueprint $table) {
            $table->integer('SoLuong')->default(1)->after('TenSach')->comment('Số lượng sách trong kho');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('SACH', function (Blueprint $table) {
            $table->dropColumn('SoLuong');
        });
    }
};
