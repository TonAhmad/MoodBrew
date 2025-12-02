<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * RoleMiddleware - Memvalidasi akses berdasarkan role user
 * 
 * Usage di route: middleware('role:admin') atau middleware('role:admin,cashier')
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Check apakah role user termasuk dalam roles yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Redirect berdasarkan role user
            return $this->redirectBasedOnRole($user->role);
        }

        return $next($request);
    }

    /**
     * Redirect user ke halaman sesuai role-nya
     * 
     * @param string $role
     * @return Response
     */
    private function redirectBasedOnRole(string $role): Response
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard')->with('error', 'Akses tidak diizinkan.'),
            'cashier' => redirect()->route('cashier.dashboard')->with('error', 'Akses tidak diizinkan.'),
            'customer' => redirect()->route('customer.home')->with('error', 'Akses tidak diizinkan.'),
            default => redirect()->route('landing.home'),
        };
    }
}
