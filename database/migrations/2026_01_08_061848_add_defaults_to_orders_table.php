<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ubah kolom existing untuk tambah default
            $table->decimal('subtotal', 12, 2)->default(0)->change();
            $table->decimal('total_amount', 12, 2)->default(0)->change(); // sesuaikan nama kolom
            // Jika ada shipping_cost
            $table->decimal('shipping_cost', 12, 2)->default(0)->change();

            // Untuk string shipping
            $table->string('shipping_name')->default('')->change();
            $table->string('shipping_address')->default('')->change();
            $table->string('shipping_phone')->default('')->change();

            // Kolom lain yang error jika ada
        });
    }

    public function down()
    {
        // Optional: revert jika perlu
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(null)->change();
            // dst...
        });
    }
};
