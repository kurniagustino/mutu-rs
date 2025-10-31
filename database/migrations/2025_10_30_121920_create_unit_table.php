<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->id(); // ID unik untuk unit
            $table->string('nama_unit', 100);
            $table->timestamps(); // Opsional, tapi bagus untuk tracking
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit');
    }
};
