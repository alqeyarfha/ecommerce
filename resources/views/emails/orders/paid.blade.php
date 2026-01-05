{{-- resources/views/emails/orders/paid.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <!-- Bootstrap 5 CDN (gunakan versi terbaru yang kompatibel dengan email) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Beberapa email client tidak mendukung link CSS eksternal dengan baik, jadi tambahkan fallback inline jika diperlukan */
        body { font-family: Arial, sans-serif; }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="card shadow">
            <div class="card-body p-5">
                <h1 class="h4 fw-bold mb-4">Halo, {{ $order->user->name }}</h1>

                <p class="lead mb-4">
                    Terima kasih! Pembayaran untuk pesanan <strong>#{{ $order->order_number }}</strong> telah kami terima.<br>
                    Kami sedang memproses pesanan Anda.
                </p>

                <!-- Tabel dengan Bootstrap -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                            </tr>
                        </thead>
                            <tr>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold bg-light">
                                <td colspan="2" class="text-end">Total</td>
                                <td class="text-end">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Button Bootstrap -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-lg px-5">
                        Lihat Detail Pesanan
                    </a>
                </div>

                <hr class="my-5">

                <p class="text-muted mb-0">
                    Jika ada pertanyaan, silakan balas email ini.
                </p>
                <p class="text-muted">
                    Salam,<br>
                    <strong>{{ config('app.name') }}</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
