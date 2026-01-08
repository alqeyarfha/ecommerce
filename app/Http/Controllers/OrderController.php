<?php

namespace App\Http\Controllers;  // ← TANPA "Admin"!!

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MidtransService;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Cek akses: hanya pemilik order yang boleh lihat
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'user']);

        $snapToken = $order->snap_token;

        if ($order->status === 'pending' && !$snapToken && $order->total_amount > 0) {
            $midtrans = new MidtransService();
            $snapToken = $midtrans->createSnapToken($order);

            if ($snapToken) {
                $order->update(['snap_token' => $snapToken]);
            }
        }

        return view('orders.show', compact('order', 'snapToken'));
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        return view('orders.success', compact('order'));
    }

    public function pending(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        return view('orders.pending', compact('order'));
    }
}
