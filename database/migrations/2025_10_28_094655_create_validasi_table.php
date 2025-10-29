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
        Schema::create('validasi', function (Blueprint $table) {
            $table->id();
            $table->integer('imut')->nullable();
            $table->string('periodevalidasi', 50)->nullable();
            $table->boolean('hasil_validasi')->default(false);
            $table->text('analisa_text')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->unsignedInteger('validated_by')->nullable(); // ✅ HANYA 1 KALI
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi');
    }
};
