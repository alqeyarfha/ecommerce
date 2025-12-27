<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * API: Generate Snap Token untuk pembayaran
     */
    public function getSnapToken(Order $order, MidtransService $midtransService)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['error' => 'Pesanan sudah dibayar.'], 400);
        }

        try {
            $snapToken = $midtransService->createSnapToken($order);
            $order->update(['snap_token' => $snapToken]);

            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Halaman: Pembayaran berhasil (redirect dari Midtrans)
     */
    public function success(Request $request, Order $order)
    {
        // Pastikan order milik user login
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Opsional: verifikasi ulang status ke Midtrans untuk keamanan
        // (disarankan di production)
        // $status = \Midtrans\Transaction::status($order->order_number);
        // if ($status->transaction_status !== 'capture' && $status->transaction_status !== 'settlement') {
        //     return redirect()->route('orders.pending', $order)->with('error', 'Pembayaran belum berhasil.');
        // }

        return view('orders.success', compact('order'));
    }

    /**
     * Halaman: Pembayaran pending / menunggu
     */
    public function pending(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.pending', compact('order'));
    }

    /**
     * Halaman: Pembayaran gagal
     */
    public function failed(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.failed', compact('order'));
    }
}
