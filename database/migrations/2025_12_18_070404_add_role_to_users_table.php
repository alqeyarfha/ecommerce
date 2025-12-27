<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom hanya jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                // Gunakan enum untuk lebih ketat dan aman
                $table->enum('role', ['customer', 'admin'])
                      ->default('customer')
                      ->after('password');

                // Opsional: tambah index jika sering query berdasarkan role
                // $table->index('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
