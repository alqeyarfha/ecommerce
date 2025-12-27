<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah google_id hanya jika belum ada
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('email');
            }

            // Tambah avatar hanya jika belum ada
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }

            // Tambah index hanya jika kolom google_id ada dan belum ada index (Laravel tidak punya hasIndex, jadi kita skip jika error)
            // Jika index sudah ada, baris ini akan error → bisa dihapus jika tidak kritis
            if (Schema::hasColumn('users', 'google_id')) {
                $table->index('google_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus index jika ada
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropIndex(['google_id']);
            }

            // Hapus kolom jika ada
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }

            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropColumn('google_id');
            }
        });
    }
};
