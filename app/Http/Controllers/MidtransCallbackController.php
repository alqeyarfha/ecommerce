<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Notification; // Jika pakai library midtrans/midtrans-php

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Verifikasi signature key (WAJIB untuk keamanan)
        $serverKey = config('midtrans.server_key'); // pastikan di config/midtrans.php
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response(['message' => 'Invalid signature'], 403);
        }

        // Ambil notification
        $notification = new Notification();

        $order = Order::where('order_number', $notification->order_id)->firstOrFail();

        $transaction = $notification->transaction_status;
        $fraud = $notification->fraud_status;

        if ($transaction == 'capture') {
            if ($fraud == 'challenge') {
                $order->payment_status = 'challenge';
            } else if ($fraud == 'accept') {
                $order->payment_status = 'paid';
                $order->status = 'paid'; // atau 'processing' sesuai flow kamu
            }
        } else if ($transaction == 'settlement') {
            $order->payment_status = 'paid';
            $order->status = 'paid';
        } else if ($transaction == 'deny') {
            $order->payment_status = 'denied';
        } else if ($transaction == 'expire') {
            $order->payment_status = 'expired';
            $order->status = 'cancelled';
        } else if ($transaction == 'cancel') {
            $order->payment_status = 'cancelled';
        } else if ($transaction == 'pending') {
            $order->payment_status = 'unpaid';
        }

        $order->save();

        return response(['message' => 'OK'], 200); // Midtrans butuh response 200
    }
}
