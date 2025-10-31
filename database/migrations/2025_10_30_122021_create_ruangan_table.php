<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ruangan', function (Blueprint $table) {
            $table->id('id_ruang'); // ID unik untuk ruangan

            // Ini adalah Foreign Key (Kunci Tamu)
            $table->unsignedBigInteger('id_unit');

            $table->string('nama_ruang', 100);
            $table->string('sink', 50)->nullable(); // Saya tambahkan lagi 'sink' dari SQL lama
            $table->timestamps(); // Opsional

            // Ini bagian terpenting: Membuat relasi
            $table->foreign('id_unit')
                ->references('id')
                ->on('unit')
                ->onDelete('restrict'); // atau 'cascade'
        });
    }

    public function down()
    {
        Schema::dropIfExists('ruangan');
    }
};
