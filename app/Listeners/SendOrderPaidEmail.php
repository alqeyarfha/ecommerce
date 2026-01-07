<?php

// app/Listeners/SendOrderPaidEmail.php

namespace App\Listeners;

use App\Events\OrderPaidEvent;
use App\Mail\OrderPaid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderPaidEmail// Email akan dikirim langsung tanpa queue

{
    public function handle(OrderPaidEvent $event): void
    {
        try {
            // Kirim email ke user
            Mail::to($event->order->user->email)
                ->send(new OrderPaid($event->order));

            Log::info('Order paid email sent successfully', [
                'order_id' => $event->order->id,
                'email'    => $event->order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order paid email', [
                'order_id' => $event->order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        // Opsional: Kirim notif ke Admin juga
    }
}
