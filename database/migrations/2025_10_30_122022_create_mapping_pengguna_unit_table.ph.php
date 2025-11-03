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
        Schema::create('mapping_pengguna_unit', function (Blueprint $table) {
            $table->id();

            // Menyambung ke tabel 'users'
            $table->unsignedInteger('user_id'); // Kita asumsikan ID user adalah integer

            // Menyambung ke tabel 'ruangan'
            // ✅ PERBAIKAN: Ubah dari string(20) menjadi unsignedBigInteger
            $table->unsignedBigInteger('id_ruang');

            $table->string('level', 50)->nullable(); // cth: 'Admin Unit', 'Operator'
            $table->timestamps();

            // ✅ TAMBAHAN: Foreign key constraints untuk menjaga integritas data
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_ruang')->references('id_ruang')->on('ruangan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_pengguna_unit');
    }
};
