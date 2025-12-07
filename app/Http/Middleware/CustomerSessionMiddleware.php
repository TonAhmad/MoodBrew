<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CustomerSessionMiddleware - Memastikan customer sudah mengisi data diri
 * 
 * Middleware ini memvalidasi bahwa customer sudah melakukan "login" 
 * dengan mengisi nama dan email sebelum bisa mengakses fitur customer.
 * 
 * Usage di route: middleware('customer.session')
 */
class CustomerSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah customer sudah mengisi data session
        $customerName = session('customer_name');
        $customerEmail = session('customer_email');

        // Jika belum ada session customer, redirect ke halaman login customer
        if (empty($customerName) || empty($customerEmail)) {
            // Simpan intended URL untuk redirect setelah login
            session()->put('url.intended', $request->url());

            return redirect()
                ->route('login')
                ->with('warning', 'Silakan isi nama dan email terlebih dahulu untuk melanjutkan.');
        }

        return $next($request);
    }
}
