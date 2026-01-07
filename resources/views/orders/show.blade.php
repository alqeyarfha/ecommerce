{{-- resources/views/orders/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">

                {{-- Header Order --}}
                <div class="card-header bg-white border-bottom py-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h3 fw-bold text-dark mb-1">
                                Order #{{ $order->order_number }}
                            </h1>
                            <p class="text-muted mb-0">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        {{-- Status Badge --}}
                        <span class="badge rounded-pill fs-6 px-3 py-2
                            @switch($order->status)
                                @case('pending')
                                    bg-warning text-dark
                                    @break
                                @case('processing')
                                    bg-primary
                                    @break
                                @case('shipped')
                                    bg-info
                                    @break
                                @case('delivered')
                                    bg-success
                                    @break
                                @case('cancelled')
                                    bg-danger
                                    @break
                            @endswitch
                        ">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                {{-- Detail Items --}}
                <div class="card-body p-4">
                    <h3 class="h5 fw-semibold mb-4">Produk yang Dipesan</h3>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="border-bottom pb-3">Produk</th>
                                    <th scope="col" class="text-center border-bottom pb-3">Qty</th>
                                    <th scope="col" class="text-end border-bottom pb-3">Harga</th>
                                    <th scope="col" class="text-end border-bottom pb-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3">{{ $item->product_name }}</td>
                                    <td class="py-3 text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 text-end">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-end">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top border-2">
                                @if($order->shipping_cost > 0)
                                <tr>
                                    <td colspan="3" class="pt-3 text-end fw-normal">Ongkos Kirim:</td>
                                    <td class="pt-3 text-end">
                                        Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="pt-2 text-end fw-bold fs-5">
                                        TOTAL BAYAR:
                                    </td>
                                    <td class="pt-2 text-end fw-bold fs-5 text-primary">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Alamat Pengiriman --}}
                <div class="card-body bg-light border-top p-4">
                    <h3 class="h5 fw-semibold mb-3">Alamat Pengiriman</h3>
                    <div class="text-dark">
                        <p class="fw-medium mb-1">{{ $order->shipping_name }}</p>
                        <p class="mb-1">{{ $order->shipping_phone }}</p>
                        <p class="mb-0">{{ $order->shipping_address }}</p>
                    </div>
                </div>

                {{-- Tombol Bayar (hanya tampil jika pending) --}}
                @if($order->status === 'pending' && $order->snap_token)
                <div class="card-body bg-primary bg-opacity-10 border-top text-center p-4">
                    <p class="text-muted mb-3">
                        Selesaikan pembayaran Anda sebelum batas waktu berakhir.
                    </p>
                    <button id="pay-button"
                            class="btn btn-primary btn-lg px-5 py-3 fw-bold">
                        💳 Bayar Sekarang
                    </button>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

{{-- Snap.js Integration --}}
@if($order->snap_token)
@push('scripts')
    {{-- Load Snap JS dari Midtrans --}}
    <script src="{{ config('midtrans.snap_url') }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');

            if (payButton) {
                payButton.addEventListener('click', function() {
                    // Disable button untuk mencegah double click
                    payButton.disabled = true;
                    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';

                    // Panggil Snap.pay dengan token dari server
                    window.snap.pay('{{ $order->snap_token }}', {

                        // ✅ Callback saat pembayaran SUKSES
                        onSuccess: function(result) {
                            console.log('Payment Success:', result);
                            /*
                             * PENTING: Jangan update database di sini!
                             * Ini hanya callback frontend, bisa dimanipulasi.
                             * Status sebenarnya akan diupdate via Webhook.
                             * Di sini kita hanya redirect untuk UX.
                             */
                            window.location.href = '{{ route("orders.success", $order) }}';
                        },

                        // ⏳ Callback saat pembayaran PENDING
                        // (User sudah dapat VA/QR tapi belum transfer)
                        onPending: function(result) {
                            console.log('Payment Pending:', result);
                            window.location.href = '{{ route("orders.pending", $order) }}';
                        },

                        // ❌ Callback saat pembayaran GAGAL
                        onError: function(result) {
                            console.log('Payment Error:', result);
                            alert('Pembayaran gagal! Silakan coba lagi.');
                            payButton.disabled = false;
                            payButton.innerHTML = '💳 Bayar Sekarang';
                        },

                        // 🚪 Callback saat popup DITUTUP tanpa menyelesaikan
                        onClose: function() {
                            console.log('Payment popup closed');
                            payButton.disabled = false;
                            payButton.innerHTML = '💳 Bayar Sekarang';
                            // Tidak perlu alert, biarkan user coba lagi
                        }
                    });
                });
            }
        });
    </script>
@endpush
@endif
