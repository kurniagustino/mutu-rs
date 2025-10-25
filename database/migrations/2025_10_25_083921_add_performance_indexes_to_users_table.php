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
        // ✅ TAMBAH INDEXES KE TABLE USERS (UNTUK PERFORMA LOGIN)
        Schema::table('users', function (Blueprint $table) {
            // Index untuk email (skip kalau sudah ada unique constraint)
            try {
                $table->index('email', 'users_email_index');
            } catch (\Exception $e) {
                // Index sudah ada, skip
            }

            // Index untuk username (skip kalau sudah ada unique constraint)
            try {
                $table->index('username', 'users_username_index');
            } catch (\Exception $e) {
                // Index sudah ada, skip
            }

            // Index untuk id_ruang (foreign key ke departemen)
            try {
                $table->index('id_ruang', 'users_id_ruang_index');
            } catch (\Exception $e) {
                // Index sudah ada, skip
            }
        });

        // ✅ TAMBAH INDEX KE TABLE DEPARTEMEN (kalau ada)
        if (Schema::hasTable('departemen')) {
            Schema::table('departemen', function (Blueprint $table) {
                try {
                    $table->index('id_ruang', 'departemen_id_ruang_index');
                } catch (\Exception $e) {
                    // Index sudah ada, skip
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropIndex('users_email_index');
            } catch (\Exception $e) {
                // Index tidak ada, skip
            }

            try {
                $table->dropIndex('users_username_index');
            } catch (\Exception $e) {
                // Index tidak ada, skip
            }

            try {
                $table->dropIndex('users_id_ruang_index');
            } catch (\Exception $e) {
                // Index tidak ada, skip
            }
        });

        if (Schema::hasTable('departemen')) {
            Schema::table('departemen', function (Blueprint $table) {
                try {
                    $table->dropIndex('departemen_id_ruang_index');
                } catch (\Exception $e) {
                    // Index tidak ada, skip
                }
            });
        }
    }
};
