<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus foreign key constraint yang lama
        Schema::table('ruangan', function (Blueprint $table) {
            $table->dropForeign(['id_unit']);
        });

        // Tambahkan foreign key baru dengan ON DELETE CASCADE
        Schema::table('ruangan', function (Blueprint $table) {
            $table->foreign('id_unit')
                ->references('id')
                ->on('unit')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        // Hapus foreign key cascade
        Schema::table('ruangan', function (Blueprint $table) {
            $table->dropForeign(['id_unit']);
        });

        // Kembalikan ke constraint RESTRICT
        Schema::table('ruangan', function (Blueprint $table) {
            $table->foreign('id_unit')
                ->references('id')
                ->on('unit')
                ->onDelete('restrict');
        });
    }
};