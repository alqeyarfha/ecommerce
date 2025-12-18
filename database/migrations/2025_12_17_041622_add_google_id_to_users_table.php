<?php

// ========================================
// FILE: database/migrations/xxxx_add_google_id_to_users_table.php
// FUNGSI: Menambahkan kolom untuk menyimpan Google user ID dan avatar
// ========================================

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
        Schema::table('users', function (Blueprint $table) {
            // Kolom untuk menyimpan Google ID (unik dari Google OAuth)
            $table->string('google_id')->nullable()->after('email');

            // Kolom untuk menyimpan URL avatar/foto profil dari Google
            $table->string('avatar')->nullable()->after('google_id');

            // Index untuk mempercepat pencarian berdasarkan google_id
            $table->index('google_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus index terlebih dahulu (wajib sebelum drop column)
            $table->dropIndex(['google_id']);

            // Hapus kolom-kolom yang ditambahkan
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
