<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

/**
 * AuthService - Business logic untuk autentikasi
 * 
 * Menangani login admin/cashier (dengan password)
 * dan quick-access customer (tanpa password, hanya email/nama)
 */
class AuthService
{
    /**
     * Login untuk admin dan cashier (memerlukan password)
     * 
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return array{success: bool, message: string, redirect?: string}
     */
    public function loginStaff(string $email, string $password, bool $remember = false): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email tidak ditemukan.',
            ];
        }

        // Pastikan hanya admin/cashier yang bisa login di halaman ini
        if (!in_array($user->role, [User::ROLE_ADMIN, User::ROLE_CASHIER])) {
            return [
                'success' => false,
                'message' => 'Akun ini bukan akun staff. Silakan gunakan akses customer.',
            ];
        }

        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Password salah.',
            ];
        }

        Auth::login($user, $remember);

        $redirect = $user->isAdmin() ? '/admin/dashboard' : '/cashier/dashboard';

        return [
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => $redirect,
        ];
    }

    /**
     * Quick access untuk customer (tanpa password)
     * Customer hanya perlu nama dan email untuk mulai
     * Ini untuk mengurangi friction sebelum menggunakan AI
     * 
     * @param string $name
     * @param string $email
     * @param string|null $tableNumber
     * @return array{success: bool, message: string, sessionId?: string}
     */
    public function customerQuickAccess(string $name, string $email, ?string $tableNumber = null): array
    {
        // Cari user existing atau buat session-based customer
        $user = User::where('email', $email)->first();

        if ($user) {
            // Jika email sudah terdaftar sebagai staff, tolak
            if ($user->isStaff()) {
                return [
                    'success' => false,
                    'message' => 'Email ini terdaftar sebagai akun staff. Gunakan halaman login staff.',
                ];
            }

            // Login customer existing
            Auth::login($user);
        } else {
            // Untuk customer baru, simpan di session (guest mode)
            // Tidak perlu create user, cukup session
            Session::put('customer_name', $name);
            Session::put('customer_email', $email);
        }

        // Simpan nomor meja di session
        if ($tableNumber) {
            Session::put('table_number', $tableNumber);
        }

        return [
            'success' => true,
            'message' => 'Selamat datang, ' . $name . '!',
            'sessionId' => Session::getId(),
        ];
    }

    /**
     * Register customer baru (optional, jika mau simpan data)
     * 
     * @param string $name
     * @param string $email
     * @param string|null $password
     * @return array{success: bool, message: string, user?: User}
     */
    public function registerCustomer(string $name, string $email, ?string $password = null): array
    {
        // Check email sudah ada
        if (User::where('email', $email)->exists()) {
            return [
                'success' => false,
                'message' => 'Email sudah terdaftar.',
            ];
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password ? Hash::make($password) : Hash::make(uniqid()), // Random password jika tidak diisi
            'role' => User::ROLE_CUSTOMER,
        ]);

        Auth::login($user);

        return [
            'success' => true,
            'message' => 'Akun berhasil dibuat!',
            'user' => $user,
        ];
    }

    /**
     * Logout user
     * 
     * @return void
     */
    public function logout(): void
    {
        // Clear customer session data
        Session::forget(['customer_name', 'customer_email', 'table_number']);

        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
    }

    /**
     * Get current customer data (dari Auth atau Session)
     * 
     * @return array{name: string|null, email: string|null, isGuest: bool, tableNumber: string|null}
     */
    public function getCurrentCustomer(): array
    {
        if (Auth::check()) {
            $user = Auth::user();
            return [
                'name' => $user->name,
                'email' => $user->email,
                'isGuest' => false,
                'tableNumber' => Session::get('table_number'),
            ];
        }

        return [
            'name' => Session::get('customer_name'),
            'email' => Session::get('customer_email'),
            'isGuest' => true,
            'tableNumber' => Session::get('table_number'),
        ];
    }

    /**
     * Check apakah ada customer session (guest atau logged in)
     * 
     * @return bool
     */
    public function hasCustomerSession(): bool
    {
        return Auth::check() || Session::has('customer_email');
    }
}
