<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // hapus order jika user dihapus
            $table->string('order_number')->unique();

            // Status pesanan
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])
                  ->default('pending');

            // Status pembayaran — ini yang menyebabkan error sebelumnya jika tidak ada
            $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'expired', 'refunded'])
                  ->default('unpaid');

            // Informasi pengiriman
            $table->string('shipping_name');
            $table->text('shipping_address'); // text lebih baik untuk alamat panjang
            $table->string('shipping_phone');

            // Biaya
            $table->decimal('subtotal', 12, 2);           // opsional: subtotal sebelum ongkir
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);

            // Midtrans
            $table->string('snap_token')->nullable();
            $table->string('payment_method')->nullable(); // opsional: simpan metode (credit_card, gopay, dll)
            $table->json('midtrans_response')->nullable(); // opsional: simpan full response untuk debug

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
