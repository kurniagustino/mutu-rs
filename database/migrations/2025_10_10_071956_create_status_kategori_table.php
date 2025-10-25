<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status');
            $table->string('warna_badge', 7)->default('#6b7280'); // Format: #RRGGBB
            $table->timestamps(); // ✅ TAMBAHKAN INI
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_kategori');
    }
};
