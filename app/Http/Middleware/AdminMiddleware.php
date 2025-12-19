<?php
// ========================================
// FILE: app/Http/Middleware/AdminMiddleware.php
// FUNGSI: Membatasi akses hanya untuk user dengan role 'admin'
// ========================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Method ini dipanggil SETIAP KALI ada request yang melewati middleware ini.
     *
     * @param Request $request  Request dari user
     * @param Closure $next     Fungsi untuk melanjutkan ke proses berikutnya
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {

            return redirect()->route('login');
        }
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');

        }
        return $next($request);
        // ↑ $next($request) = Lanjutkan ke controller tujuan
    }
}
